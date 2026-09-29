<?php
/** Case study card. Props: cs (row with metrics), index, heading (h2|h3) */
$h    = $heading ?? 'h3';
$href = path('case-studies/' . $cs['slug'] . '/');
?>
<article class="case-card" data-reveal style="--delay:<?= 0.08 * ($index ?? 0) ?>s" data-category="<?= e($cs['category_slug'] ?? '') ?>">
    <a class="case-card__link" href="<?= e($href) ?>" data-cursor="view" aria-label="View case study: <?= e($cs['title']) ?>"></a>
    <div class="case-card__media">
        <?php if (!empty($cs['hero_image'])): ?>
            <?= picture($cs['hero_image'], $cs['title'] . ' — project visual', 'loading="lazy" decoding="async" width="800" height="520"') ?>
        <?php else: ?>
            <?= visual($cs['visual'], $cs['title'] . ' — abstract project visual') ?>
        <?php endif; ?>
        <span class="case-card__arrow" aria-hidden="true"><?= icon('arrow-up-right') ?></span>
    </div>
    <div class="case-card__body">
        <div class="case-card__meta">
            <span class="tag tag--accent"><?= e($cs['tag_label'] ?: ($cs['category_name'] ?? '')) ?></span>
            <span class="case-card__industry"><?= e($cs['industry']) ?></span>
        </div>
        <<?= $h ?> class="case-card__title"><?= e($cs['title']) ?></<?= $h ?>>
        <p class="case-card__text"><?= e($cs['excerpt']) ?></p>
        <?php if (!empty($cs['metrics'])): ?>
            <dl class="case-card__kpis">
                <?php foreach (array_slice($cs['metrics'], 0, 3) as $m): ?>
                    <div><dt><?= e($m['label']) ?></dt><dd><?= e($m['value']) ?></dd></div>
                <?php endforeach; ?>
            </dl>
        <?php endif; ?>
        <span class="arrow-link case-card__cta" aria-hidden="true">View Case Study <?= icon('arrow-right') ?></span>
    </div>
</article>
