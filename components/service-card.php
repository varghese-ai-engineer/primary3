<?php
/** Core service card. Props: service (row), index (int) */
$features = json_list($service['features']);
$vis      = $service['meta'] ?: 'network';
?>
<article class="service-card" data-reveal style="--delay:<?= 0.1 * $index ?>s" id="service-<?= e($service['slug']) ?>">
    <div class="service-card__glow" aria-hidden="true"></div>
    <div class="service-card__visual" aria-hidden="true"><?= visual($vis) ?></div>
    <div class="service-card__body">
        <div class="service-card__top">
            <span class="service-card__icon"><?= icon($service['icon'] ?: 'sparkles') ?></span>
            <span class="service-card__num">0<?= $index + 1 ?></span>
        </div>
        <h3 class="service-card__title"><?= e($service['title']) ?></h3>
        <p class="service-card__text"><?= e($service['excerpt']) ?></p>
        <?php if ($features): ?>
            <ul class="check-list">
                <?php foreach ($features as $f): ?>
                    <li><?= icon('check') ?><?= e($f) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($service['cta_label']): ?>
            <a class="arrow-link service-card__cta" href="<?= e(path($service['cta_url'] ?: '/ai-solutions/')) ?>">
                <?= e($service['cta_label']) ?><span class="sr-only"> about <?= e($service['title']) ?></span><?= icon('arrow-right') ?>
            </a>
        <?php endif; ?>
    </div>
</article>
