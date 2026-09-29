<?php
/**
 * PDO database layer.
 * - MySQL is the production driver.
 * - SQLite is supported for zero-config local previews / CI tests.
 * All queries use prepared statements; never interpolate user input.
 */
declare(strict_types=1);

final class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }
        $c = config('db');
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        if ($c['driver'] === 'sqlite') {
            self::$pdo = new PDO('sqlite:' . $c['sqlite'], null, null, $opts);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
        } else {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['name']);
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], $opts);
        }
        return self::$pdo;
    }

    public static function driver(): string
    {
        return config('db')['driver'] === 'sqlite' ? 'sqlite' : 'mysql';
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $r = self::run($sql, $params)->fetch();
        return $r === false ? null : $r;
    }

    public static function value(string $sql, array $params = [])
    {
        $r = self::run($sql, $params)->fetchColumn();
        return $r === false ? null : $r;
    }

    /** Insert an associative array. Column names are validated against a strict pattern. */
    public static function insert(string $table, array $data): int
    {
        self::assertIdent($table);
        $cols = array_keys($data);
        array_walk($cols, [self::class, 'assertIdent']);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $cols),
            implode(', ', array_fill(0, count($cols), '?'))
        );
        self::run($sql, array_values($data));
        return (int)self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, int $id): void
    {
        self::assertIdent($table);
        $sets = [];
        foreach (array_keys($data) as $col) {
            self::assertIdent($col);
            $sets[] = "$col = ?";
        }
        $params   = array_values($data);
        $params[] = $id;
        self::run(sprintf('UPDATE %s SET %s WHERE id = ?', $table, implode(', ', $sets)), $params);
    }

    public static function delete(string $table, int $id): void
    {
        self::assertIdent($table);
        self::run("DELETE FROM $table WHERE id = ?", [$id]);
    }

    public static function assertIdent(string $name): void
    {
        if (!preg_match('/^[a-z_][a-z0-9_]{0,63}$/', $name)) {
            throw new InvalidArgumentException('Invalid SQL identifier');
        }
    }
}
