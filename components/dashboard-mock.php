<?php /** Illustrative dashboard UI (HTML/CSS) used on analytics case studies. */ ?>
<figure class="dash" data-reveal aria-label="Illustration of the natural-language analytics dashboard">
    <div class="dash__bar">
        <span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="dash__url">analytics.app / ask</span>
    </div>
    <div class="dash__body">
        <div class="dash__query">
            <?= icon('sparkles') ?>
            <span class="dash__typed" data-typed="Show me revenue by region for Q3">Show me revenue by region for Q3</span>
            <span class="dash__kbd">↵</span>
        </div>
        <div class="dash__grid">
            <div class="dash__card"><span class="dash__k">Total revenue</span><span class="dash__v">$4.82M</span><span class="dash__d">▲ 18.4%</span></div>
            <div class="dash__card"><span class="dash__k">Top region</span><span class="dash__v">APAC</span><span class="dash__d">▲ 27.1%</span></div>
            <div class="dash__card"><span class="dash__k">Query time</span><span class="dash__v">1.4s</span><span class="dash__d">12 sources</span></div>
        </div>
        <div class="dash__chart" aria-hidden="true">
            <?php foreach ([['N. America', 78], ['Europe', 62], ['APAC', 92], ['LATAM', 41], ['MEA', 33]] as $i => [$r, $v]): ?>
                <div class="dash__row"><span><?= $r ?></span><span class="dash__track"><span class="dash__fill" style="--w:<?= $v ?>%;--delay:<?= .1 * $i ?>s"></span></span></div>
            <?php endforeach; ?>
        </div>
    </div>
    <figcaption class="dash__cap">Illustrative interface — sample data.</figcaption>
</figure>
