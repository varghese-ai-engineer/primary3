<?php
/**
 * Application bootstrap — included by every entry point
 * (front controller, admin screens, installer).
 */
declare(strict_types=1);

define('HT_START', microtime(true));

require __DIR__ . '/helpers.php';
require __DIR__ . '/security.php';
require __DIR__ . '/db.php';
require __DIR__ . '/icons.php';
require __DIR__ . '/visuals.php';
require __DIR__ . '/repository.php';
require __DIR__ . '/seo.php';
require __DIR__ . '/uploads.php';

date_default_timezone_set((string)(config('timezone') ?: 'UTC'));
mb_internal_encoding('UTF-8');

$__debug = (bool)config('debug');
error_reporting(E_ALL);
ini_set('display_errors', $__debug ? '1' : '0');
ini_set('log_errors', '1');

// Convert warnings/notices to exceptions so nothing silently breaks.
set_error_handler(static function (int $no, string $str, string $file, int $line) {
    if (!(error_reporting() & $no)) {
        return false;
    }
    throw new ErrorException($str, 0, $no, $file, $line);
});

set_exception_handler(static function (Throwable $ex) use ($__debug) {
    log_error(get_class($ex) . ': ' . $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine());
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
    }
    if (defined('HT_API') || is_ajax()) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode(['ok' => false, 'message' => 'Something went wrong on our side. Please try again shortly.']);
        return;
    }
    $isDbError = $ex instanceof PDOException;
    $debugInfo = $__debug ? $ex->getMessage() . "\n" . $ex->getFile() . ':' . $ex->getLine() : null;
    require root_path('pages/500.php');
});

start_secure_session();
