<?php
/**
 * Button / CTA link.
 * Props: label, href, variant (primary|ghost|link), size (sm|md|lg), icon, magnetic, class, type (for <button>), attrs
 */
$variant  = $variant ?? 'primary';
$size     = $size ?? 'md';
$icon     = $icon ?? 'arrow-right';
$classes  = trim('btn btn--' . $variant . ' btn--' . $size . ' ' . ($class ?? ''));
$magnetic = !empty($magnetic) ? ' data-magnetic' : '';
$attrs    = $attrs ?? '';
$inner    = '<span class="btn__label">' . e($label) . '</span>' . ($icon ? '<span class="btn__icon">' . icon($icon) . '</span>' : '');
if (!empty($href)): ?>
<a class="<?= e($classes) ?>" href="<?= e(path($href)) ?>"<?= $magnetic ?> <?= $attrs ?>><?= $inner ?></a>
<?php else: ?>
<button class="<?= e($classes) ?>" type="<?= e($type ?? 'button') ?>"<?= $magnetic ?> <?= $attrs ?>><?= $inner ?></button>
<?php endif;
