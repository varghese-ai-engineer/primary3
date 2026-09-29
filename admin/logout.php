<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';

if (!is_post()) {               // logout is state-changing: POST + CSRF only
    admin_redirect('index.php');
}
require_csrf();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax']);
}
session_destroy();
admin_redirect('login.php?out=1');
