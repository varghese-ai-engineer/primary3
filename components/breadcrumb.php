<?php
/** Breadcrumb trail. Props: items = [[label, path], ...] (last item = current page) */
$last = count($items) - 1;
?>
<nav class="breadcrumb hero-seq" style="--seq:0" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($items as $i => [$label, $href]): ?>
            <li>
                <?php if ($i < $last): ?>
                    <a href="<?= e(path($href)) ?>"><?= e($label) ?></a><span class="breadcrumb__sep" aria-hidden="true">/</span>
                <?php else: ?>
                    <span aria-current="page"><?= e($label) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
