<?php
/**
 * Section heading with eyebrow, masked-line title and description.
 * Props: eyebrow, title, description, align (left|center), tag (h1|h2), class, id
 */
$tag   = in_array($tag ?? 'h2', ['h1', 'h2', 'h3'], true) ? ($tag ?? 'h2') : 'h2';
$align = ($align ?? 'left') === 'center' ? ' section-head--center' : '';
?>
<div class="section-head<?= $align ?> <?= e($class ?? '') ?>">
    <?php if (!empty($eyebrow)): ?>
        <p class="eyebrow" data-reveal><span class="eyebrow__dot" aria-hidden="true"></span><?= e($eyebrow) ?></p>
    <?php endif; ?>
    <<?= $tag ?> class="section-title" data-lines<?= !empty($id) ? ' id="' . e($id) . '"' : '' ?>><?= split_lines($title) ?></<?= $tag ?>>
    <?php if (!empty($description)): ?>
        <p class="section-desc" data-reveal style="--delay:.15s"><?= e($description) ?></p>
    <?php endif; ?>
</div>
