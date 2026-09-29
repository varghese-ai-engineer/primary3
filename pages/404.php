<?php
/** 404 — not found */
$page['key']        = '404';
$page['title']      = 'Page not found';
$page['description']= 'The page you are looking for does not exist or has moved.';
$page['noindex']    = true;
$page['body_class'] = 'page-error';
?>
<section class="error-page" aria-labelledby="err-title">
    <div class="page-hero__atmos" aria-hidden="true"></div>
    <div class="grid-bg" aria-hidden="true"></div>
    <div class="container error-page__inner">
        <p class="error-page__code hero-seq" style="--seq:0" aria-hidden="true">404</p>
        <p class="eyebrow hero-seq" style="--seq:1"><span class="eyebrow__dot" aria-hidden="true"></span>Signal lost</p>
        <h1 class="display-sm hero-seq" style="--seq:1" id="err-title">This route isn't in our graph.</h1>
        <p class="lead hero-seq" style="--seq:2">The page you requested doesn't exist or has moved. Let's get you back on track.</p>
        <div class="btn-row btn-row--center hero-seq" style="--seq:3">
            <?php component('button', ['label' => 'Back to Home', 'href' => '/', 'size' => 'lg']); ?>
            <?php component('button', ['label' => 'View Case Studies', 'href' => '/case-studies/', 'size' => 'lg', 'variant' => 'ghost', 'icon' => 'arrow-up-right']); ?>
        </div>
    </div>
</section>
