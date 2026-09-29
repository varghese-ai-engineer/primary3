<?php
/** About */
$page['title']       = 'About Us — Built by Engineers, Driven by Impact';
$page['description'] = setting('about_hero_description');
$page['breadcrumbs'] = [['Home', '/'], ['About', '/about/']];

$team = team();
$tl   = timeline();
$people = [];
foreach ($team as $m) {
    $people[] = array_filter([
        '@type' => 'Person', 'name' => $m['name'], 'jobTitle' => $m['role'],
        'worksFor' => ['@id' => url('/#organization')], 'sameAs' => $m['linkedin'] ?: null,
    ]);
}
$page['schema'] = [['@type' => 'AboutPage', 'name' => 'About ' . setting('site_name'), 'url' => url('about/')], ...$people];

$years = (int)date('Y') - (int)setting('founding_year', '2014');

component('page-hero', [
    'eyebrow' => setting('about_hero_eyebrow'), 'title' => setting('about_hero_title'),
    'description' => setting('about_hero_description'), 'breadcrumbs' => $page['breadcrumbs'],
]);
?>
<!-- Story -->
<section class="section" aria-labelledby="story-title">
    <div class="container story">
        <div class="story__head">
            <?php component('section-heading', ['eyebrow' => 'Our story', 'title' => setting('about_story_heading'), 'id' => 'story-title']); ?>
            <div class="story__stats" data-reveal>
                <div><span class="story__stat-v"><?= $years ?>+</span><span class="story__stat-l">Years engineering</span></div>
                <div><span class="story__stat-v"><?= count($tl) ?></span><span class="story__stat-l">Milestones</span></div>
                <div><span class="story__stat-v">24/7</span><span class="story__stat-l">Agent uptime</span></div>
            </div>
        </div>
        <div class="story__body prose prose--lg" data-reveal style="--delay:.1s"><?= paragraphs(setting('about_story')) ?></div>
    </div>
</section>

<!-- Team -->
<?php if ($team): ?>
<section class="section section--alt" aria-labelledby="team-title">
    <div class="container">
        <?php component('section-heading', ['eyebrow' => 'Leadership', 'title' => setting('about_team_heading'), 'id' => 'team-title']); ?>
        <div class="team-grid">
            <?php foreach ($team as $i => $m) component('team-card', ['member' => $m, 'index' => $i]); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Journey -->
<?php if ($tl): ?>
<section class="section" aria-labelledby="journey-title">
    <div class="section__atmos section__atmos--right" aria-hidden="true"></div>
    <div class="container">
        <?php component('section-heading', ['eyebrow' => 'Since ' . setting('founding_year', '2014'), 'title' => setting('about_timeline_heading'), 'id' => 'journey-title', 'align' => 'center']); ?>
        <div class="timeline-wrap" data-timeline>
        <span class="timeline__line" aria-hidden="true"><span class="timeline__fill"></span></span>
        <ol class="timeline">
            <?php foreach ($tl as $i => $item): ?>
                <li class="timeline__item<?= $i === count($tl) - 1 ? ' timeline__item--now' : '' ?>" data-reveal>
                    <span class="timeline__dot" aria-hidden="true"></span>
                    <div class="timeline__card">
                        <span class="timeline__year"><?= e($item['year']) ?></span>
                        <h3 class="timeline__title"><?= e($item['title']) ?></h3>
                        <?php if ($item['description']): ?><p class="timeline__text"><?= e($item['description']) ?></p><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        </div>
    </div>
</section>
<?php endif; ?>

<?php component('cta', [
    'heading'     => setting('about_cta_heading'),
    'description' => setting('about_cta_description'),
    'primary'     => ['Get in Touch', '/contact/'],
    'secondary'   => ['See Our Work', '/case-studies/'],
]); ?>
