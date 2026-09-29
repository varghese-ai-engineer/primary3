<?php
/**
 * Admin bootstrap: session auth, idle timeout, CSRF-guarded POSTs,
 * login throttling and shared admin helpers.
 */
declare(strict_types=1);

define('HT_ADMIN', true);
require dirname(__DIR__, 2) . '/includes/bootstrap.php';

if (!config('installed')) {
    redirect('/install.php');
}
send_security_headers(true);
ob_start();   // lets the error handler replace a half-rendered page with a clean 500

function admin_url(string $p = ''): string
{
    return path('admin/' . ltrim($p, '/'));
}

function admin_redirect(string $p): never
{
    header('Location: ' . admin_url($p), true, 303);
    exit;
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): array
{
    $a = current_admin();
    $idle = config('admin')['session_minutes'] * 60;
    $fingerprint = hash('sha256', (string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
    if (!$a || ($_SESSION['admin_fp'] ?? '') !== $fingerprint || (time() - ($_SESSION['admin_last'] ?? 0)) > $idle) {
        unset($_SESSION['admin'], $_SESSION['admin_last'], $_SESSION['admin_fp']);
        if (is_ajax()) {
            json_response(['ok' => false, 'message' => 'Session expired'], 401);
        }
        $back = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . admin_url('login.php' . ($a ? '?expired=1' : '') . ($back ? ($a ? '&' : '?') . 'next=' . rawurlencode($back) : '')), true, 303);
        exit;
    }
    $_SESSION['admin_last'] = time();
    return $a;
}

/** Abort any state-changing request that lacks a valid CSRF token. */
function require_csrf(): void
{
    if (!csrf_valid()) {
        if (is_ajax()) {
            json_response(['ok' => false, 'message' => 'Invalid CSRF token — reload the page.'], 419);
        }
        http_response_code(419);
        exit('Security token expired. Go back, reload the page and try again.');
    }
}

/* ---------- Login throttling ---------- */
function login_locked(string $email): bool
{
    $cfg   = config('admin');
    $since = date('Y-m-d H:i:s', time() - $cfg['lock_minutes'] * 60);
    $byIp  = (int)DB::value('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > ?', [client_ip(), $since]);
    $byEm  = (int)DB::value('SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempted_at > ?', [strtolower($email), $since]);
    return $byIp >= $cfg['max_attempts'] * 2 || $byEm >= $cfg['max_attempts'];
}

function record_failed_login(string $email): void
{
    DB::insert('login_attempts', ['ip' => client_ip(), 'email' => mb_substr(strtolower($email), 0, 190), 'attempted_at' => now()]);
    // housekeeping
    DB::run('DELETE FROM login_attempts WHERE attempted_at < ?', [date('Y-m-d H:i:s', time() - 86400)]);
}

function clear_login_attempts(string $email): void
{
    DB::run('DELETE FROM login_attempts WHERE email = ? OR ip = ?', [strtolower($email), client_ip()]);
}

/* ---------- Misc ---------- */
function status_badge(string $status): string
{
    $map = ['new' => ['New', 'violet'], 'read' => ['Read', 'grey'], 'contacted' => ['Contacted', 'green']];
    [$label, $tone] = $map[$status] ?? [ucfirst($status), 'grey'];
    return '<span class="a-badge a-badge--' . $tone . '">' . e($label) . '</span>';
}

function time_ago(?string $dt): string
{
    if (!$dt) return '—';
    $d = time() - strtotime($dt);
    if ($d < 60) return 'just now';
    if ($d < 3600) return floor($d / 60) . ' min ago';
    if ($d < 86400) return floor($d / 3600) . ' h ago';
    if ($d < 604800) return floor($d / 86400) . ' d ago';
    return date('M j, Y', strtotime($dt));
}

function unread_count(): int
{
    return (int)DB::value("SELECT COUNT(*) FROM contact_submissions WHERE status = 'new'");
}
