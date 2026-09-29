<?php
/** Closing CTA — the visual climax. Props: heading, description, primary [label, href], secondary [label, href] */
?>
<section class="cta-section" aria-labelledby="cta-title" data-cta>
    <div class="container">
        <div class="cta-box">
            <div class="cta-box__glow" aria-hidden="true"></div>
            <div class="cta-box__orb" aria-hidden="true"></div>
            <div class="grid-bg grid-bg--fade" aria-hidden="true"></div>
            <div class="cta-box__content">
                <h2 class="cta-box__title display-sm" id="cta-title" data-lines><?= split_lines($heading) ?></h2>
                <?php if (!empty($description)): ?>
                    <p class="cta-box__desc lead" data-reveal style="--delay:.25s"><?= e($description) ?></p>
                <?php endif; ?>
                <div class="btn-row btn-row--center" data-reveal style="--delay:.4s">
                    <?php component('button', ['label' => $primary[0], 'href' => $primary[1], 'size' => 'lg', 'magnetic' => true, 'class' => 'glow']); ?>
                    <?php if (!empty($secondary)) component('button', ['label' => $secondary[0], 'href' => $secondary[1], 'size' => 'lg', 'variant' => 'ghost', 'icon' => 'arrow-up-right']); ?>
                </div>
            </div>
        </div>
    </div>
</section>
