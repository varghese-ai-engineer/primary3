<?php
/** Case studies listing — filterable (AJAX with no-JS fallback via ?category=) */
$page['title']       = 'Case Studies — Proven Results, Real Impact';
$page['description'] = setting('cs_hero_description');
$page['breadcrumbs'] = [['Home', '/'], ['Case Studies', '/case-studies/']];

$cats    = categories();
$current = preg_match('/^[a-z0-9-]{1,90}$/', (string)($_GET['category'] ?? '')) ? $_GET['category'] : null;
if ($current && !in_array($current, array_column($cats, 'slug'), true)) {
    $current = null;
}
$items = case_studies($current);
if ($current) {
    $page['canonical'] = url('case-studies/');   // filtered views canonicalise to the main list
}

$list = [];
foreach (case_studies() as $cs) {
    $list[] = ['@type' => 'ListItem', 'position' => count($list) + 1, 'url' => url('case-studies/' . $cs['slug'] . '/'), 'name' => $cs['title']];
}
$page['schema'] = [['@type' => 'CollectionPage', 'name' => 'Case Studies', 'url' => url('case-studies/'), 'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => $list]]];

component('page-hero', [
    'eyebrow' => setting('cs_hero_eyebrow'), 'title' => setting('cs_hero_title'),
    'description' => setting('cs_hero_description'), 'breadcrumbs' => $page['breadcrumbs'],
]);
?>
<section class="section section--cases" aria-label="Case study list">
    <div class="container">
        <div class="filters" role="group" aria-label="Filter case studies by category" data-filters>
            <a class="filter<?= !$current ? ' is-active' : '' ?>" href="<?= path('/case-studies/') ?>" data-filter="" aria-pressed="<?= !$current ? 'true' : 'false' ?>">
                All Projects <span class="filter__count"><?= array_sum(array_column($cats, 'total')) ?></span>
            </a>
            <?php foreach ($cats as $c): ?>
                <a class="filter<?= $current === $c['slug'] ? ' is-active' : '' ?>" href="<?= path('/case-studies/?category=' . rawurlencode($c['slug'])) ?>" data-filter="<?= e($c['slug']) ?>" aria-pressed="<?= $current === $c['slug'] ? 'true' : 'false' ?>">
                    <?= e($c['name']) ?> <span class="filter__count"><?= (int)$c['total'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="sr-only" aria-live="polite" data-filter-status><?= count($items) ?> case studies shown</p>

        <div class="case-grid" data-case-grid>
            <?php if ($items): ?>
                <?php foreach ($items as $i => $cs) component('case-study-card', ['cs' => $cs, 'index' => $i, 'heading' => 'h2']); ?>
            <?php else: ?>
                <?php component('empty-state', ['title' => 'No case studies here yet', 'text' => 'We are writing up new projects in this category. In the meantime, explore our other work.']); ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php component('cta', [
    'heading'     => setting('cs_cta_heading'),
    'description' => setting('cs_cta_description'),
    'primary'     => [setting('cs_cta_label', 'Start Your Project'), '/contact/'],
]); ?>
