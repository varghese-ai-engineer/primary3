<?php
/** @var string $title  @var string $active */
$__me   = current_admin();
$__unread = unread_count();
$__nav = [
    'Overview' => [
        ['index.php', 'Dashboard', 'home', 'dashboard'],
        ['messages.php', 'Contact Messages', 'inbox', 'messages'],
    ],
    'Content' => [
        ['case-studies.php', 'Case Studies', 'layers', 'case-studies'],
        ['resource.php?r=categories', 'Categories', 'git-branch', 'categories'],
        ['resource.php?r=services', 'Services & Process', 'workflow', 'services'],
        ['resource.php?r=metrics', 'Metrics', 'bar-chart', 'metrics'],
        ['resource.php?r=team', 'Team', 'users', 'team'],
        ['resource.php?r=timeline', 'Company Timeline', 'clock', 'timeline'],
        ['resource.php?r=technologies', 'Technologies', 'cpu', 'technologies'],
        ['resource.php?r=clients', 'Client Logos', 'star', 'clients'],
        ['resource.php?r=testimonials', 'Testimonials', 'quote', 'testimonials'],
        ['resource.php?r=faqs', 'FAQs', 'message', 'faqs'],
    ],
    'Site' => [
        ['media.php', 'Media Library', 'image', 'media'],
        ['settings.php', 'Site Settings', 'settings', 'settings'],
        ['resource.php?r=seo', 'SEO Settings', 'search', 'seo'],
        ['resource.php?r=social', 'Social Links', 'globe', 'social'],
    ],
];
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> · Admin · <?= e(setting('site_name')) ?></title>
    <link rel="icon" href="<?= path('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <script src="<?= asset('js/admin.js') ?>" defer></script>
</head>
<body class="admin">
<a class="skip" href="#a-main">Skip to content</a>
<aside class="a-side" id="a-side">
    <a class="a-brand" href="<?= admin_url('index.php') ?>">
        <img src="<?= path('assets/img/favicon.svg') ?>" alt="" width="30" height="30">
        <span><?= e(setting('logo_text', 'Primary')) ?><small>Admin</small></span>
    </a>
    <nav class="a-nav" aria-label="Admin">
        <?php foreach ($__nav as $__group => $__items): ?>
            <p class="a-nav__group"><?= e($__group) ?></p>
            <?php foreach ($__items as [$__href, $__label, $__ic, $__key]): ?>
                <a class="a-nav__link<?= ($active ?? '') === $__key ? ' is-active' : '' ?>" href="<?= admin_url($__href) ?>"<?= ($active ?? '') === $__key ? ' aria-current="page"' : '' ?>>
                    <?= icon($__ic) ?><span><?= e($__label) ?></span>
                    <?php if ($__key === 'messages' && $__unread): ?><span class="a-count"><?= $__unread ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
</aside>
<div class="a-wrap">
    <header class="a-top">
        <button class="a-burger" type="button" aria-controls="a-side" aria-expanded="false" data-side-toggle aria-label="Toggle menu"><?= icon('menu') ?></button>
        <h1 class="a-top__title"><?= e($title) ?></h1>
        <div class="a-top__right">
            <a class="a-btn a-btn--ghost a-btn--sm" href="<?= path('/') ?>" target="_blank" rel="noopener"><?= icon('eye') ?>View site</a>
            <a class="a-user" href="<?= admin_url('profile.php') ?>" title="Profile"><span><?= e(mb_substr($__me['name'], 0, 1)) ?></span><?= e($__me['name']) ?></a>
            <form method="post" action="<?= admin_url('logout.php') ?>"><?= csrf_field() ?><button class="a-icon-btn" title="Sign out" aria-label="Sign out"><?= icon('logout') ?></button></form>
        </div>
    </header>
    <main class="a-main" id="a-main">
        <?php if ($__f = flash('ok')): ?><div class="a-alert a-alert--ok" role="status"><?= icon('check') ?><?= e($__f) ?></div><?php endif; ?>
        <?php if ($__f = flash('err')): ?><div class="a-alert a-alert--err" role="alert"><?= icon('alert') ?><?= e($__f) ?></div><?php endif; ?>
