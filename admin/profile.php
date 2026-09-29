<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me   = require_admin();
$user = DB::one('SELECT * FROM users WHERE id = ?', [$me['id']]);
$errors = [];

if (is_post()) {
    require_csrf();
    $name  = mb_substr(trim((string)($_POST['name'] ?? '')), 0, 120);
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $cur   = (string)($_POST['current_password'] ?? '');
    $new   = (string)($_POST['new_password'] ?? '');
    $conf  = (string)($_POST['confirm_password'] ?? '');

    if ($name === '') $errors['name'] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email.';
    elseif (DB::value('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, $me['id']])) $errors['email'] = 'Email already in use.';
    if (!password_verify($cur, $user['password_hash'])) $errors['current_password'] = 'Current password is incorrect.';
    if ($new !== '') {
        if (strlen($new) < 10 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) $errors['new_password'] = 'Use at least 10 characters with letters and numbers.';
        elseif ($new !== $conf) $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $data = ['name' => $name, 'email' => $email, 'updated_at' => now()];
        if ($new !== '') {
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            session_regenerate_id(true);
        }
        DB::update('users', $data, (int)$me['id']);
        $_SESSION['admin']['name']  = $name;
        $_SESSION['admin']['email'] = $email;
        flash('ok', 'Profile updated.');
        admin_redirect('profile.php');
    }
    $user = array_merge($user, ['name' => $name, 'email' => $email]);
}

$title  = 'Your profile';
$active = '';
require __DIR__ . '/_inc/layout-top.php';
$err = fn($k) => isset($errors[$k]) ? '<small class="a-error">' . e($errors[$k]) . '</small>' : '';
?>
<form class="a-card a-form-grid" method="post" novalidate style="max-width:760px">
    <?= csrf_field() ?>
    <div class="a-field<?= isset($errors['name']) ? ' has-error' : '' ?>"><label for="p_name">Name</label><input id="p_name" name="name" value="<?= e($user['name']) ?>" required><?= $err('name') ?></div>
    <div class="a-field<?= isset($errors['email']) ? ' has-error' : '' ?>"><label for="p_email">Email</label><input id="p_email" type="email" name="email" value="<?= e($user['email']) ?>" required autocomplete="username"><?= $err('email') ?></div>
    <div class="a-field<?= isset($errors['new_password']) ? ' has-error' : '' ?>"><label for="p_new">New password <small class="a-muted">(optional)</small></label><input id="p_new" type="password" name="new_password" autocomplete="new-password"><?= $err('new_password') ?></div>
    <div class="a-field<?= isset($errors['confirm_password']) ? ' has-error' : '' ?>"><label for="p_conf">Confirm new password</label><input id="p_conf" type="password" name="confirm_password" autocomplete="new-password"><?= $err('confirm_password') ?></div>
    <div class="a-field a-field--wide<?= isset($errors['current_password']) ? ' has-error' : '' ?>"><label for="p_cur">Current password <span class="a-req">*</span></label><input id="p_cur" type="password" name="current_password" required autocomplete="current-password"><small class="a-help">Required to save any change.</small><?= $err('current_password') ?></div>
    <div class="a-actions"><button class="a-btn a-btn--primary"><?= icon('check') ?>Save profile</button></div>
    <p class="a-muted a-field--wide">Last sign-in: <?= e($user['last_login_at'] ? date('M j, Y · H:i', strtotime($user['last_login_at'])) : '—') ?></p>
</form>
<?php require __DIR__ . '/_inc/layout-bottom.php';
