<?php
/** Sticky, scroll-driven process. Props: steps (services rows, section = process) */
if (empty($steps)) {
    return;
}
$total = count($steps);
?>
<div class="process" data-process style="--steps:<?= $total ?>">
    <aside class="process__rail" aria-hidden="true">
        <div class="process__rail-inner">
            <div class="process__big"><span data-process-num>01</span><small>/ 0<?= $total ?></small></div>
            <ol class="process__nav">
                <?php foreach ($steps as $i => $s): ?>
                    <li class="process__nav-item<?= $i === 0 ? ' is-active' : '' ?>" data-process-nav="<?= $i ?>">
                        <span class="process__nav-num">0<?= $i + 1 ?></span><?= e($s['title']) ?>
                    </li>
                <?php endforeach; ?>
            </ol>
            <div class="process__track"><span class="process__fill"></span></div>
        </div>
    </aside>

    <ol class="process__steps">
        <?php foreach ($steps as $i => $s): $del = json_list($s['features']); ?>
            <li class="process-step<?= $i === 0 ? ' is-active' : '' ?>" data-process-step="<?= $i ?>">
                <div class="process-step__head">
                    <span class="process-step__num">0<?= $i + 1 ?></span>
                    <span class="process-step__icon"><?= icon($s['icon'] ?: 'sparkles') ?></span>
                    <?php if ($s['meta']): ?><span class="chip"><?= icon('clock') ?><?= e($s['meta']) ?></span><?php endif; ?>
                </div>
                <h3 class="process-step__title"><?= e($s['title']) ?></h3>
                <p class="process-step__text"><?= e($s['excerpt']) ?></p>
                <?php if ($del): ?>
                    <p class="process-step__label">Deliverables</p>
                    <ul class="tag-list">
                        <?php foreach ($del as $d): ?><li class="tag"><?= icon('check') ?><?= e($d) ?></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <div class="process-step__progress" aria-hidden="true"><span style="width:<?= round(($i + 1) / $total * 100) ?>%"></span></div>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
