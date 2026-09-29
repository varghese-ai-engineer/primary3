<?php
declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('ht_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => (base_path() ?: '') . '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($sent) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $sent);
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** HMAC-signed value (used by the contact-form time trap). */
function sign_value(string $v): string
{
    return $v . '.' . hash_hmac('sha256', $v, (string)config('key'));
}

function verify_signed(string $signed): ?string
{
    $pos = strrpos($signed, '.');
    if ($pos === false) {
        return null;
    }
    $v = substr($signed, 0, $pos);
    return hash_equals(hash_hmac('sha256', $v, (string)config('key')), substr($signed, $pos + 1)) ? $v : null;
}

function send_security_headers(bool $admin = false): void
{
    if (headers_sent()) {
        return;
    }
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');
    $csp = "default-src 'self'; "
         . "img-src 'self' data: https:; "
         . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
         . "font-src 'self' https://fonts.gstatic.com data:; "
         . "script-src 'self'; "
         . "connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'";
    header('Content-Security-Policy: ' . $csp);
    if ($admin) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('X-Robots-Tag: noindex, nofollow');
    }
}
