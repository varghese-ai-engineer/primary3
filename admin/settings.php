<?php
/** Site settings — every editable text on the public site, grouped by page. */
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
require __DIR__ . '/_inc/form.php';
$me = require_admin();

$groups = [
    'general'     => 'General',
    'contact'     => 'Contact & Footer',
    'home'        => 'Home page',
    'ai'          => 'AI Solutions',
    'cases'       => 'Case Studies',
    'about'       => 'About',
    'contactpage' => 'Get Started page',
    'legal'       => 'Legal pages',
    'seo'         => 'SEO defaults',
];
$group = isset($groups[$_GET['g'] ?? '']) ? $_GET['g'] : 'general';
$rows  = DB::all('SELECT * FROM site_settings WHERE group_name = ? ORDER BY sort_order, id', [$group]);

$toField = static function (array $r): array {
    $type = match ($r['type']) {
        'textarea', 'list' => 'textarea',
        'email' => 'email',
        'image' => 'image',
        'url'   => 'url',
        default => 'text',
    };
    return ['type' => $type, 'label' => $r['label'], 'rows' => $r['type'] === 'list' ? 5 : (str_contains($r['setting_key'], '_content') || $r['setting_key'] === 'about_story' ? 12 : 3), 'max' => $type === 'textarea' ? 20000 : 500];
};

$errors = [];
if (is_post()) {
    require_csrf();
    $fields = [];
    foreach ($rows as $r) {
        $fields[$r['setting_key']] = $toField($r);
    }
    [$data, $errors] = collect_fields($fields, $me['id']);
    if (!$errors) {
        foreach ($data as $k => $v) {
            DB::run('UPDATE site_settings SET value = ? WHERE setting_key = ?', [(string)$v, $k]);
        }
        flash('ok', $groups[$group] . ' settings saved.');
        admin_redirect('settings.php?g=' . $group);
    }
    foreach ($rows as &$r) {
        $r['value'] = $data[$r['setting_key']] ?? $r['value'];
    }
    unset($r);
}

$title  = 'Site Settings';
$active = 'settings';
require __DIR__ . '/_inc/layout-top.php';
?>
<div class="a-tabs a-tabs--wrap">
    <?php foreach ($groups as $k => $l): ?>
        <a class="a-tab<?= $group === $k ? ' is-active' : '' ?>" href="<?= admin_url('settings.php?g=' . $k) ?>"><?= e($l) ?></a>
    <?php endforeach; ?>
</div>
<p class="a-intro">Tip: in headings, a new line starts a new animated line, and wrapping words in <code>*asterisks*</code> applies the gradient accent.</p>
<?php if ($errors): ?><div class="a-alert a-alert--err" role="alert"><?= icon('alert') ?>Please fix the highlighted fields.</div><?php endif; ?>
<form class="a-card a-form-grid" method="post" enctype="multipart/form-data" novalidate data-dirty-guard>
    <?= csrf_field() ?>
    <?php foreach ($rows as $r) echo render_field($r['setting_key'], $toField($r), $r['value'], $errors[$r['setting_key']] ?? null); ?>
    <div class="a-actions"><button class="a-btn a-btn--primary"><?= icon('check') ?>Save <?= e(strtolower($groups[$group])) ?></button></div>
</form>
<?php require __DIR__ . '/_inc/layout-bottom.php';
