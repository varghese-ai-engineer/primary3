<?php
/**
 * Shared installer logic used by the web installer (/install.php)
 * and the CLI installer (php database/install.php).
 */
declare(strict_types=1);

require_once __DIR__ . '/seed.php';

/** Convert the canonical MySQL schema into SQLite DDL (for local previews/tests). */
function ht_sqlite_schema(string $mysql): array
{
    $mysql = preg_replace('/--[^\n]*\n/', "\n", $mysql);
    $statements = [];
    foreach (array_filter(array_map('trim', explode(';', $mysql))) as $stmt) {
        if (!preg_match('/^CREATE TABLE (\w+)/i', $stmt, $m)) {
            continue; // skip SET / DROP
        }
        $table   = $m[1];
        $indexes = [];
        $lines   = [];
        foreach (preg_split('/\R/', $stmt) as $line) {
            $t = trim($line);
            if (preg_match('/^INDEX (\w+) \(([^)]+)\),?$/i', $t, $im)) {
                $indexes[] = "CREATE INDEX {$im[1]} ON $table ({$im[2]})";
                continue;
            }
            $lines[] = $line;
        }
        $sql = implode("\n", $lines);
        $sql = preg_replace('/INT UNSIGNED AUTO_INCREMENT PRIMARY KEY/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $sql = preg_replace('/\)\s*ENGINE=.*$/is', ')', $sql);
        $sql = preg_replace('/,\s*\)$/', "\n)", $sql);   // trailing comma after removed INDEX lines
        $statements[] = $sql;
        array_push($statements, ...$indexes);
    }
    return $statements;
}

function ht_run_schema(): void
{
    $sql = file_get_contents(__DIR__ . '/schema.mysql.sql');
    $pdo = DB::pdo();
    if (DB::driver() === 'sqlite') {
        $pdo->exec('PRAGMA foreign_keys = OFF');
        foreach ($pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN) as $t) {
            $pdo->exec('DROP TABLE IF EXISTS "' . str_replace('"', '', $t) . '"');
        }
        foreach (ht_sqlite_schema($sql) as $s) {
            $pdo->exec($s);
        }
        $pdo->exec('PRAGMA foreign_keys = ON');
        return;
    }
    foreach (array_filter(array_map('trim', explode(';', preg_replace('/--[^\n]*\n/', "\n", $sql)))) as $s) {
        $pdo->exec($s);
    }
}

function ht_install(string $name, string $email, string $password): void
{
    if (DB::driver() === 'sqlite') {
        $file = config('db')['sqlite'];
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0775, true);
        }
    }
    ht_run_schema();
    $pdo = DB::pdo();
    $pdo->beginTransaction();
    try {
        ht_seed();
        DB::insert('users', [
            'name'          => $name,
            'email'         => strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'admin',
        ]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    file_put_contents(root_path('storage/installed.lock'), date('c'));
}
