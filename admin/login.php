<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';

if (current_admin()) {
    admin_redirect('index.php');
}

$error = null;
$email = '';
$next  = (string)($_GET['next'] ?? $_POST['next'] ?? '');
// Only allow local admin paths as redirect targets (no open redirects).
if (!preg_match('#^' . preg_quote(base_path() . '/admin/', '#') . '[a-z0-9\-]+\.php(\?[\w=&%\-.]*)?$#i', $next)) {
    $next = '';
}

if (is_post()) {
    require_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $pass  = (string)($_POST['password'] ?? '');

    if (login_locked($email)) {
        $error = 'Too many failed attempts. Please wait ' . config('admin')['lock_minutes'] . ' minutes and try again.';
    } else {
        $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? DB::one('SELECT * FROM users WHERE email = ?', [$email]) : null;
        // Constant-ish time: always run password_verify.
        $hash = $user['password_hash'] ?? '$2y$10$7W1oX0k1zv3oO2mJvQn8cOMtL8Zr7yqg0vCzJZq8m3n5JfG6rYk2S';
        $ok   = password_verify($pass, $hash) && $user;
        if ($ok) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                DB::update('users', ['password_hash' => password_hash($pass, PASSWORD_DEFAULT)], (int)$user['id']);
            }
            session_regenerate_id(true);
            unset($_SESSION['_csrf']);
            $_SESSION['admin']      = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']];
            $_SESSION['admin_last'] = time();
            $_SESSION['admin_fp']   = hash('sha256', (string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
            clear_login_attempts($email);
            DB::update('users', ['last_login_at' => now()], (int)$user['id']);
            header('Location: ' . ($next ?: admin_url('index.php')), true, 303);
            exit;
        }
        record_failed_login($email);
        $error = 'Incorrect email or password.';
    }
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · Admin</title>
    <link rel="icon" href="<?= path('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth">
<main class="auth__card">
    <img class="auth__logo" src="<?= path('assets/img/favicon.svg') ?>" alt="" width="44" height="44">
    <h1 class="auth__title">Welcome back</h1>
    <p class="auth__sub">Sign in to manage <?= e(setting('site_name')) ?>.</p>
    <?php if (isset($_GET['expired'])): ?><div class="a-alert a-alert--warn" role="status">Your session expired. Please sign in again.</div><?php endif; ?>
    <?php if (isset($_GET['out'])): ?><div class="a-alert a-alert--ok" role="status">You have been signed out.</div><?php endif; ?>
    <?php if ($error): ?><div class="a-alert a-alert--err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="a-form" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <label class="a-field"><span>Email</span><input type="email" name="email" value="<?= e($email) ?>" autocomplete="username" required autofocus></label>
        <label class="a-field"><span>Password</span><input type="password" name="password" autocomplete="current-password" required></label>
        <button class="a-btn a-btn--primary a-btn--block" type="submit">Sign in</button>
    </form>
    <a class="auth__back" href="<?= path('/') ?>">← Back to website</a>
</main>
</body>
</html>
