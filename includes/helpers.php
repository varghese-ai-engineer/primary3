<?php
declare(strict_types=1);

/* ------------------------------------------------------------------ */
/*  Config & paths                                                     */
/* ------------------------------------------------------------------ */

function config(?string $key = null)
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require dirname(__DIR__) . '/config/config.php';
    }
    return $key === null ? $cfg : ($cfg[$key] ?? null);
}

function root_path(string $p = ''): string
{
    return config('root') . ($p !== '' ? '/' . ltrim($p, '/') : '');
}

/** Base path (sub-folder installs) derived from APP_URL, e.g. "/site". */
function base_path(): string
{
    static $bp = null;
    if ($bp === null) {
        $bp = rtrim((string)parse_url(config('url') ?: '', PHP_URL_PATH), '/');
    }
    return $bp;
}

/** Absolute URL when APP_URL is set, otherwise root-relative. */
function url(string $path = '/'): string
{
    if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:') || str_starts_with($path, '#')) {
        return $path;
    }
    $base = config('url') ?: '';
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base   = isset($_SERVER['HTTP_HOST']) ? $scheme . '://' . $_SERVER['HTTP_HOST'] : '';
    }
    return $base . '/' . ltrim($path, '/');
}

/** Root-relative path (used for internal links so they work behind proxies). */
function path(string $p = '/'): string
{
    if (preg_match('#^(https?:)?//#', $p) || preg_match('#^(mailto|tel):#', $p) || str_starts_with($p, '#')) {
        return $p;
    }
    return base_path() . '/' . ltrim($p, '/');
}

/** Cache-busted asset URL. */
function asset(string $p): string
{
    $file = root_path('assets/' . ltrim($p, '/'));
    $v    = is_file($file) ? substr((string)filemtime($file), -6) : '1';
    return path('assets/' . ltrim($p, '/')) . '?v=' . $v;
}

/** Public URL for an uploaded file path stored in the DB (e.g. "uploads/2026/09/x.jpg"). */
function upload_url(?string $p): string
{
    if (!$p) {
        return '';
    }
    if (preg_match('#^(https?:)?//#', $p)) {
        return $p;
    }
    return path(ltrim($p, '/'));
}

function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $bp  = base_path();
    if ($bp !== '' && str_starts_with($uri, $bp)) {
        $uri = substr($uri, strlen($bp)) ?: '/';
    }
    return '/' . ltrim(rawurldecode($uri), '/');
}

/* ------------------------------------------------------------------ */
/*  Output                                                             */
/* ------------------------------------------------------------------ */

function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escaped text with paragraphs from blank lines and <br> from single newlines. */
function paragraphs(?string $text, string $class = ''): string
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }
    $out = '';
    foreach (preg_split("/\R{2,}/", $text) as $p) {
        $out .= '<p' . ($class ? ' class="' . e($class) . '"' : '') . '>' . nl2br(e(trim($p)), false) . '</p>';
    }
    return $out;
}

/**
 * Split a heading into masked lines for the editorial reveal.
 * Lines are separated by newlines (or "|"). Wrap text in *asterisks* for accent gradient.
 */
function split_lines(?string $text): string
{
    $lines = preg_split('/\R|\s*\|\s*/', trim((string)$text));
    $html  = '';
    foreach ($lines as $i => $line) {
        $safe = e($line);
        $safe = preg_replace('/\*(.+?)\*/', '<em class="text-gradient">$1</em>', $safe);
        $html .= '<span class="line"><span class="line__inner" style="--i:' . $i . '">' . $safe . '</span></span>';
    }
    return $html;
}

/** Plain version of a split_lines() string (for meta/alt). */
function plain_heading(?string $text): string
{
    return trim(preg_replace('/\s+/', ' ', str_replace(['*', '|'], ['', ' '], (string)$text)));
}

function json_list($v): array
{
    if (is_array($v)) {
        return $v;
    }
    $d = json_decode((string)$v, true);
    return is_array($d) ? array_values(array_filter($d, fn($x) => $x !== '' && $x !== null)) : [];
}

/** Convert multi-line textarea input into a JSON array string. */
function lines_to_json(?string $v): string
{
    $items = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$v)), 'strlen'));
    return json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-') ?: 'item';
}

function excerpt(?string $s, int $len = 160): string
{
    $s = trim(preg_replace('/\s+/', ' ', strip_tags((string)$s)));
    return mb_strlen($s) > $len ? rtrim(mb_substr($s, 0, $len - 1)) . '…' : $s;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/* ------------------------------------------------------------------ */
/*  Views / components                                                 */
/* ------------------------------------------------------------------ */

/** Render a reusable component from /components with isolated scope. */
function component(string $name, array $props = []): void
{
    $file = root_path('components/' . basename($name) . '.php');
    if (!is_file($file)) {
        throw new RuntimeException("Component not found: $name");
    }
    (static function (string $__file, array $props) {
        extract($props, EXTR_SKIP);
        include $__file;
    })($file, $props);
}

function render_component(string $name, array $props = []): string
{
    ob_start();
    component($name, $props);
    return (string)ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  HTTP                                                               */
/* ------------------------------------------------------------------ */

function redirect(string $to, int $code = 302): never
{
    header('Location: ' . (preg_match('#^https?://#', $to) ? $to : path($to)), true, $code);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function is_ajax(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input(string $key, $default = '')
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function flash(string $type, ?string $msg = null)
{
    if ($msg === null) {
        $m = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $m;
    }
    $_SESSION['_flash'][$type] = $msg;
    return null;
}

function log_error(string $msg): void
{
    $dir = root_path('storage/logs');
    if (is_dir($dir) && is_writable($dir)) {
        @file_put_contents($dir . '/app-' . date('Y-m') . '.log', '[' . now() . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else {
        error_log($msg);
    }
}
