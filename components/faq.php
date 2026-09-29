<?php
/** FAQ accordion (progressively enhanced <details>). Props: items (rows), heading */
if (empty($items)) {
    return;
}
?>
<div class="faq" data-faq>
    <?php foreach ($items as $i => $f): ?>
        <details class="faq__item" data-reveal style="--delay:<?= 0.06 * $i ?>s">
            <summary class="faq__q">
                <span><?= e($f['question']) ?></span>
                <span class="faq__icon" aria-hidden="true"><?= icon('plus') ?></span>
            </summary>
            <div class="faq__a"><div class="faq__a-inner"><?= paragraphs($f['answer']) ?></div></div>
        </details>
    <?php endforeach; ?>
</div>
