<?php
/**
 * Infinite marquee. Props: items (strings), label, variant (logos|ticker), reverse (bool)
 * Content is duplicated once for a seamless loop; the copy is hidden from assistive tech.
 */
$variant = $variant ?? 'logos';
$items   = array_values(array_filter($items));
if (!$items) {
    return;
}
$render = static function (array $items, bool $hidden) use ($variant) {
    $out = '<ul class="marquee__group"' . ($hidden ? ' aria-hidden="true"' : '') . '>';
    foreach ($items as $it) {
        $out .= $variant === 'ticker'
            ? '<li class="marquee__item marquee__item--ticker">' . e($it) . '<span class="ticker-dot" aria-hidden="true"></span></li>'
            : '<li class="marquee__item"><span class="client-logo"><span class="client-logo__mark" aria-hidden="true">' . e(mb_substr($it, 0, 1)) . '</span>' . e($it) . '</span></li>';
    }
    return $out . '</ul>';
};
?>
<div class="marquee marquee--<?= e($variant) ?><?= !empty($reverse) ? ' marquee--reverse' : '' ?>" <?= !empty($label) ? 'role="region" aria-label="' . e($label) . '"' : '' ?>>
    <div class="marquee__track">
        <?= $render($items, false) ?><?= $render($items, true) ?>
    </div>
</div>
