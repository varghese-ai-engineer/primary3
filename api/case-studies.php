<?php
/**
 * GET /api/case-studies?category=slug
 * Returns server-rendered card HTML so the markup stays identical to the page.
 */
declare(strict_types=1);

if (!defined('HT_API')) {           // only reachable through the front controller
    http_response_code(404);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    json_response(['ok' => false, 'message' => 'Method not allowed'], 405);
}

$slug = (string)($_GET['category'] ?? '');
if ($slug !== '' && !preg_match('/^[a-z0-9-]{1,90}$/', $slug)) {
    json_response(['ok' => false, 'message' => 'Invalid category'], 422);
}

$items = case_studies($slug !== '' ? $slug : null);
$html  = '';
foreach ($items as $i => $cs) {
    $html .= render_component('case-study-card', ['cs' => $cs, 'index' => $i, 'heading' => 'h2']);
}
if (!$items) {
    $html = render_component('empty-state', [
        'title' => 'No matching projects',
        'text'  => 'Nothing in this category yet — try another filter or browse all projects.',
    ]);
}

header('Cache-Control: public, max-age=60');
json_response(['ok' => true, 'count' => count($items), 'html' => $html]);
