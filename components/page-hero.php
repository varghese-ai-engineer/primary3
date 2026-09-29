<?php
/**
 * Inner-page hero. Props: eyebrow, title, description, breadcrumbs, class, aside (html)
 */
?>
<section class="page-hero <?= e($class ?? '') ?>" aria-labelledby="page-title">
    <div class="page-hero__atmos" aria-hidden="true" data-parallax="0.15"></div>
    <div class="grid-bg" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <?php if (!empty($breadcrumbs)) component('breadcrumb', ['items' => $breadcrumbs]); ?>
        <p class="eyebrow hero-seq" style="--seq:0"><span class="eyebrow__dot" aria-hidden="true"></span><?= e($eyebrow ?? '') ?></p>
        <h1 class="page-hero__title display hero-seq--lines" id="page-title"><?= split_lines($title) ?></h1>
        <?php if (!empty($description)): ?>
            <p class="page-hero__desc lead hero-seq" style="--seq:2"><?= e($description) ?></p>
        <?php endif; ?>
        <?php if (!empty($aside)): ?>
            <div class="page-hero__aside hero-seq" style="--seq:3"><?= $aside ?></div>
        <?php endif; ?>
    </div>
</section>
