<?php
/** Home page */
$page['title']       = 'AI Agents, Workflow Automation & Custom Web Apps';
$page['description'] = setting('hero_description');
$page['schema']      = [schema_services(services('core'))];

$heroTech = technologies('hero');
$featured = case_studies(null, true, 3);
?>
<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__atmos" aria-hidden="true" data-parallax="0.2"></div>
    <div class="grid-bg grid-bg--hero" aria-hidden="true"></div>
    <div class="container hero__inner">
        <div class="hero__content">
            <p class="eyebrow hero-seq" style="--seq:0"><span class="eyebrow__dot" aria-hidden="true"></span><?= e(setting('hero_eyebrow')) ?></p>
            <h1 class="hero__title display hero-seq--lines" id="hero-title"><?= split_lines(setting('hero_title')) ?></h1>
            <p class="hero__pillars hero-seq" style="--seq:2">
                <span><?= icon('bot') ?>AI Agents</span>
                <span><?= icon('workflow') ?>Workflow Automation</span>
                <span><?= icon('code') ?>Custom Web Applications</span>
            </p>
            <p class="hero__desc lead hero-seq" style="--seq:2"><?= e(setting('hero_description')) ?></p>
            <div class="btn-row hero-seq" style="--seq:3">
                <?php component('button', ['label' => setting('hero_cta_primary'), 'href' => setting('hero_cta_primary_url', '/contact/'), 'size' => 'lg', 'magnetic' => true, 'class' => 'glow']); ?>
                <?php component('button', ['label' => setting('hero_cta_secondary'), 'href' => setting('hero_cta_secondary_url', '/case-studies/'), 'size' => 'lg', 'variant' => 'ghost', 'icon' => 'arrow-up-right']); ?>
            </div>
            <?php if ($heroTech): ?>
                <div class="hero__stack hero-seq" style="--seq:4">
                    <span class="hero__stack-label">Powered by</span>
                    <ul class="badge-list">
                        <?php foreach ($heroTech as $t): ?><li class="badge"><?= e($t['name']) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="hero__visual hero-seq hero-seq--scale" style="--seq:4">
            <?php component('hero-network'); ?>
        </div>
    </div>
    <a class="scroll-cue" href="#clients" aria-label="Scroll to content"><span></span></a>
</section>

<!-- ============ CLIENTS ============ -->
<?php if ($c = clients()): ?>
<section class="clients" id="clients" aria-label="Clients">
    <div class="container">
        <p class="clients__label" data-reveal><?= e(setting('clients_heading')) ?></p>
    </div>
    <?php component('marquee', ['items' => array_column($c, 'name'), 'label' => 'Client logos']); ?>
</section>
<?php endif; ?>

<!-- ============ SERVICES ============ -->
<section class="section section--services" id="services" aria-labelledby="services-title">
    <div class="section__atmos section__atmos--left" aria-hidden="true"></div>
    <div class="container">
        <?php component('section-heading', [
            'eyebrow' => setting('services_eyebrow'), 'title' => setting('services_heading'),
            'description' => setting('services_description'), 'id' => 'services-title',
        ]); ?>
        <div class="service-grid">
            <?php foreach (services('core') as $i => $s) component('service-card', ['service' => $s, 'index' => $i]); ?>
        </div>
    </div>
</section>

<!-- ============ TECH TICKER ============ -->
<?php if ($ticker = technologies('ticker')): ?>
<section class="ticker-band" aria-label="Technologies we use">
    <?php component('marquee', ['items' => array_column($ticker, 'name'), 'variant' => 'ticker', 'reverse' => true]); ?>
</section>
<?php endif; ?>

<!-- ============ METRICS ============ -->
<?php if ($m = metrics()): ?>
<section class="section section--metrics" aria-labelledby="metrics-title">
    <div class="container">
        <div class="metrics-wrap">
            <?php component('section-heading', ['eyebrow' => 'By the numbers', 'title' => setting('metrics_heading'), 'id' => 'metrics-title']); ?>
            <div class="metrics">
                <?php foreach ($m as $i => $row) component('metric', ['metric' => $row, 'index' => $i]); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FEATURED WORK ============ -->
<?php if ($featured): ?>
<section class="section section--alt" aria-labelledby="work-title">
    <div class="container">
        <div class="section-head-row">
            <?php component('section-heading', ['eyebrow' => 'Case studies', 'title' => setting('work_heading'), 'id' => 'work-title']); ?>
            <a class="arrow-link" href="<?= path('/case-studies/') ?>" data-reveal>All case studies <?= icon('arrow-right') ?></a>
        </div>
        <div class="case-grid case-grid--home">
            <?php foreach ($featured as $i => $cs) component('case-study-card', ['cs' => $cs, 'index' => $i]); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php component('cta', [
    'heading'     => setting('cta_heading'),
    'description' => setting('cta_description'),
    'primary'     => [setting('cta_primary'), '/contact/'],
    'secondary'   => [setting('cta_secondary'), '/ai-solutions/'],
]); ?>
