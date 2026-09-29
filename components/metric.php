<?php
/**
 * Metric / counter. Props: metric (row: label, value, prefix, suffix, decimals, animate), index
 * Only rows flagged "animate" (real statistics) count up; others render statically.
 */
$dec   = (int)$metric['decimals'];
$value = number_format((float)$metric['value'], $dec, '.', '');
$shown = e($metric['prefix']) . $value . e($metric['suffix']);
?>
<div class="metric" data-reveal style="--delay:<?= 0.1 * ($index ?? 0) ?>s">
    <p class="metric__value" aria-label="<?= e(strip_tags($metric['prefix'] . $value . $metric['suffix'])) ?>">
        <?php if ($metric['animate']): ?>
            <span aria-hidden="true"><?= e($metric['prefix']) ?><span data-count="<?= e($value) ?>" data-decimals="<?= $dec ?>"><?= $value ?></span><span class="metric__suffix"><?= e($metric['suffix']) ?></span></span>
        <?php else: ?>
            <span aria-hidden="true"><?= $shown ?></span>
        <?php endif; ?>
    </p>
    <p class="metric__label"><?= e($metric['label']) ?></p>
    <span class="metric__bar" aria-hidden="true"></span>
</div>
