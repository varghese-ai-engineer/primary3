<?php
/** AI Solutions */
$page['title']       = 'AI Solutions — Agentic Workflows & Automation';
$page['description'] = setting('ai_hero_description');
$page['breadcrumbs'] = [['Home', '/'], ['AI Solutions', '/ai-solutions/']];
$page['schema']      = [schema_services(array_merge(services('core'), services('automation')))];

$features   = services('agent_feature');
$automation = services('automation');
$stack      = technologies_by_category();
$steps      = services('process');
$catIcons   = ['AI & LLMs' => 'brain', 'Automation' => 'workflow', 'Web & Cloud' => 'globe', 'Data & Storage' => 'database'];

component('page-hero', [
    'eyebrow' => setting('ai_hero_eyebrow'), 'title' => setting('ai_hero_title'),
    'description' => setting('ai_hero_description'), 'breadcrumbs' => $page['breadcrumbs'],
    'aside' => '<ul class="hero-jump">'
        . '<li><a href="#agentic-workflows">' . icon('network') . 'Agentic Workflows</a></li>'
        . '<li><a href="#automation">' . icon('workflow') . 'Automation</a></li>'
        . '<li><a href="#stack">' . icon('layers') . 'Tech Stack</a></li>'
        . '<li><a href="#process">' . icon('compass') . 'Process</a></li></ul>',
]);
?>

<!-- ============ AGENTIC WORKFLOWS ============ -->
<section class="section section--flow" id="agentic-workflows" aria-labelledby="flow-title">
    <div class="section__atmos" aria-hidden="true"></div>
    <div class="container">
        <?php component('section-heading', [
            'eyebrow' => 'Core offering', 'title' => setting('ai_workflow_heading'),
            'description' => setting('ai_workflow_description'), 'id' => 'flow-title', 'align' => 'center',
        ]); ?>
        <?php component('workflow'); ?>
    </div>
</section>

<!-- ============ AGENT FEATURES ============ -->
<?php if ($features): ?>
<section class="section section--alt" aria-labelledby="features-title">
    <div class="container">
        <?php component('section-heading', ['eyebrow' => 'Agent capabilities', 'title' => 'What makes our agents *production-ready*', 'id' => 'features-title']); ?>
        <div class="feature-grid">
            <?php foreach ($features as $i => $f): ?>
                <article class="feature" data-reveal style="--delay:<?= .08 * $i ?>s">
                    <span class="feature__num">0<?= $i + 1 ?></span>
                    <span class="feature__icon"><?= icon($f['icon'] ?: 'sparkles') ?></span>
                    <h3 class="feature__title"><?= e($f['title']) ?></h3>
                    <p class="feature__text"><?= e($f['excerpt']) ?></p>
                    <?php if ($list = json_list($f['features'])): ?>
                        <ul class="check-list check-list--sm">
                            <?php foreach ($list as $x): ?><li><?= icon('check') ?><?= e($x) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ AUTOMATION ============ -->
<?php if ($automation): ?>
<section class="section" id="automation" aria-labelledby="automation-title">
    <div class="section__atmos section__atmos--right" aria-hidden="true"></div>
    <div class="container">
        <?php component('section-heading', [
            'eyebrow' => 'Workflow automation', 'title' => setting('ai_automation_heading'),
            'description' => setting('ai_automation_description'), 'id' => 'automation-title',
        ]); ?>
        <div class="auto-grid">
            <?php foreach ($automation as $i => $a): ?>
                <article class="auto-card" data-reveal style="--delay:<?= .06 * $i ?>s">
                    <div class="auto-card__glow" aria-hidden="true"></div>
                    <span class="auto-card__icon"><?= icon($a['icon'] ?: 'sparkles') ?></span>
                    <h3 class="auto-card__title"><?= e($a['title']) ?></h3>
                    <p class="auto-card__text"><?= e($a['excerpt']) ?></p>
                    <span class="auto-card__idx" aria-hidden="true">0<?= $i + 1 ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ TECH STACK ============ -->
<?php if ($stack): ?>
<section class="section section--tint" id="stack" aria-labelledby="stack-title">
    <div class="container">
        <?php component('section-heading', ['eyebrow' => 'Technology', 'title' => setting('ai_stack_heading'), 'id' => 'stack-title', 'align' => 'center']); ?>
        <div class="stack-grid">
            <?php $ci = 0; foreach ($stack as $cat => $items): ?>
                <div class="stack-col" data-reveal style="--delay:<?= .08 * $ci++ ?>s">
                    <h3 class="stack-col__title"><?= icon($catIcons[$cat] ?? 'layers') ?><?= e($cat) ?><span class="stack-col__count"><?= count($items) ?></span></h3>
                    <ul class="stack-list">
                        <?php foreach ($items as $t): ?>
                            <li class="tech-chip"><span class="tech-chip__mark" aria-hidden="true"><?= e(mb_substr($t['name'], 0, 1)) ?></span><?= e($t['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ PROCESS ============ -->
<section class="section" id="process" aria-labelledby="process-title">
    <div class="container">
        <?php component('section-heading', [
            'eyebrow' => 'Delivery', 'title' => setting('ai_process_heading'),
            'description' => 'A four-stage delivery model that de-risks AI projects and ships working software every two weeks.',
            'id' => 'process-title',
        ]); ?>
        <?php component('process', ['steps' => $steps]); ?>
    </div>
</section>

<?php component('cta', [
    'heading'     => setting('ai_cta_heading'),
    'description' => setting('ai_cta_description'),
    'primary'     => [setting('ai_cta_label', 'Get in Touch'), '/contact/'],
    'secondary'   => ['See Case Studies', '/case-studies/'],
]); ?>
