<?php
/**
 * 500 / database fallback. Fully standalone: must not depend on the DB,
 * settings or components, because it renders when those have failed.
 * Variables (optional): $isDbError, $debugInfo
 */
$bp = function_exists('base_path') ? base_path() : '';
$css = $bp . '/assets/css/main.css';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= !empty($isDbError) ? 'Temporarily unavailable' : 'Something went wrong' ?> | Primary Infotech</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <link rel="icon" href="<?= htmlspecialchars($bp) ?>/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body class="page-error">
<main id="main">
    <section class="error-page" aria-labelledby="err-title">
        <div class="page-hero__atmos" aria-hidden="true"></div>
        <div class="grid-bg" aria-hidden="true"></div>
        <div class="container error-page__inner">
            <p class="error-page__code" aria-hidden="true"><?= !empty($isDbError) ? '503' : '500' ?></p>
            <p class="eyebrow"><span class="eyebrow__dot" aria-hidden="true"></span><?= !empty($isDbError) ? 'Maintenance' : 'Unexpected error' ?></p>
            <h1 class="display-sm" id="err-title"><?= !empty($isDbError) ? "We'll be right back." : 'Something went wrong on our side.' ?></h1>
            <p class="lead"><?= !empty($isDbError)
                ? 'Our systems are briefly unavailable while we perform maintenance. Please try again in a few minutes.'
                : 'Our engineers have been notified. Please try again, or reach us at contact@primaryinfotech.com.' ?></p>
            <div class="btn-row btn-row--center">
                <a class="btn btn--primary btn--lg" href="<?= htmlspecialchars($bp ?: '/') ?>"><span class="btn__label">Back to Home</span></a>
                <a class="btn btn--ghost btn--lg" href="mailto:contact@primaryinfotech.com"><span class="btn__label">Email us</span></a>
            </div>
            <?php if (!empty($debugInfo)): ?>
                <pre class="error-page__debug"><?= htmlspecialchars($debugInfo) ?></pre>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
