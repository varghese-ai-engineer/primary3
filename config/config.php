<?php
/**
 * Central configuration.
 * Values come from a .env file. The loader looks ONE LEVEL ABOVE the web
 * root first (recommended — never web-accessible), then the project root.
 */
declare(strict_types=1);

if (!function_exists('ht_load_env')) {
    function ht_load_env(array $paths): array
    {
        foreach ($paths as $file) {
            if (!is_file($file) || !is_readable($file)) {
                continue;
            }
            $vars = [];
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = array_map('trim', explode('=', $line, 2));
                // strip inline comments for unquoted values
                if ($v !== '' && $v[0] !== '"' && $v[0] !== "'") {
                    $v = trim(preg_replace('/\s+#.*$/', '', $v));
                }
                $v = trim($v, "\"'");
                $vars[$k] = $v;
            }
            return $vars;
        }
        return [];
    }
}

$root = dirname(__DIR__);
$env  = ht_load_env([dirname($root) . '/.env', $root . '/.env']);
$e    = static fn(string $k, $d = null) => $env[$k] ?? getenv($k) ?: $d;
$bool = static fn($v) => in_array(strtolower((string)$v), ['1', 'true', 'yes', 'on'], true);

return [
    'root'      => $root,
    'env'       => $e('APP_ENV', 'production'),
    'debug'     => $bool($e('APP_DEBUG', 'false')),
    'url'       => rtrim((string)$e('APP_URL', ''), '/'),
    'timezone'  => $e('APP_TIMEZONE', 'UTC'),
    'key'       => $e('APP_KEY', 'insecure-default-key'),
    'installed' => is_file($root . '/storage/installed.lock'),

    'db' => [
        'driver'  => $e('DB_DRIVER', 'mysql'),
        'host'    => $e('DB_HOST', 'localhost'),
        'port'    => (int)$e('DB_PORT', 3306),
        'name'    => $e('DB_NAME', ''),
        'user'    => $e('DB_USER', ''),
        'pass'    => $e('DB_PASS', ''),
        'sqlite'  => $root . '/' . ltrim((string)$e('DB_SQLITE_PATH', 'storage/database.sqlite'), '/'),
    ],

    'mail' => [
        'driver'    => $e('MAIL_DRIVER', 'mail'),
        'from'      => $e('MAIL_FROM', 'no-reply@localhost'),
        'from_name' => $e('MAIL_FROM_NAME', 'Website'),
        'host'      => $e('SMTP_HOST', ''),
        'port'      => (int)$e('SMTP_PORT', 587),
        'user'      => $e('SMTP_USER', ''),
        'pass'      => $e('SMTP_PASS', ''),
        'secure'    => $e('SMTP_SECURE', 'tls'),
    ],

    'upload_max_bytes' => (int)$e('UPLOAD_MAX_MB', 5) * 1024 * 1024,
    'upload_mimes'     => [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ],

    'admin' => [
        'session_minutes' => (int)$e('ADMIN_SESSION_MINUTES', 60),
        'max_attempts'    => (int)$e('LOGIN_MAX_ATTEMPTS', 5),
        'lock_minutes'    => (int)$e('LOGIN_LOCK_MINUTES', 15),
    ],
];
