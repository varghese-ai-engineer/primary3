<?php
/** @var array $page  @var string $content */
$seo = seo_resolve($page);
?><!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070707">
    <meta name="color-scheme" content="dark">
    <?= seo_head($seo) ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="icon" href="<?= path('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= path('assets/img/logo-mark.png') ?>">
    <script src="<?= asset('js/head.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>" defer></script>
</head>
<body class="<?= e($page['body_class'] ?? '') ?>">
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="scroll-progress" aria-hidden="true"><span></span></div>
    <div class="page-veil" aria-hidden="true"></div>

    <?php require root_path('includes/navbar.php'); ?>

    <main id="main" tabindex="-1">
        <?= $content ?>
    </main>

    <?php require root_path('includes/footer.php'); ?>

    <div class="cursor" aria-hidden="true"><span class="cursor__dot"></span><span class="cursor__ring"><span class="cursor__label">View Project <?= icon('arrow-right') ?></span></span></div>
</body>
</html>
