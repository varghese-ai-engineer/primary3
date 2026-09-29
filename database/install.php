<?php
/**
 * CLI installer:
 *   php database/install.php "Admin Name" admin@example.com 'StrongPassword!'
 * WARNING: drops and recreates all tables.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/includes/helpers.php';
require dirname(__DIR__) . '/includes/db.php';
require __DIR__ . '/installer.php';

date_default_timezone_set((string)(config('timezone') ?: 'UTC'));

[$_, $name, $email, $pass] = array_pad($argv, 4, null);
if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string)$pass) < 10) {
    fwrite(STDERR, "Usage: php database/install.php \"Name\" email@example.com 'password-min-10-chars'\n");
    exit(1);
}

ht_install($name, $email, $pass);
echo "Installed. Driver: " . DB::driver() . ". Admin: $email\n";
