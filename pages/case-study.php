<?php
/** Case study detail — challenge → solution → results storytelling */
$cs = case_study($params['slug'] ?? '');
if (!$cs) {
    return 404;
}

$page['key']         = 'case-study-' . $cs['slug'];
$page['title']       = $cs['seo_title'] ?: $cs['title'] . ' — Case Study';
$page['description'] = $cs['seo_description'] ?: $cs['excerpt'];
$page['og_image']    = $cs['og_image'] ?: $cs['hero_image'];
$page['type']        = 'article';
$page['body_class']  = 'page-case-study';
$page['breadcrumbs'] = [['Home', '/'], ['Case Studies', '/case-studies/'], [$cs['title'], '/case-studies/' . $cs['slug'] . '/']];
$page['schema']      = [schema_case_study($cs)];

$tags  = json_list($cs['tech_tags']);
$arch  = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$cs['architecture']))));
$t     = $cs['testimonial'];
$next  = next_case_study($cs);

$chapters = array_filter([
    'challenge'    => $cs['challenge'] ? 'The Challenge' : null,
    'solution'     => $cs['solution'] ? 'Our Solution' : null,
    'technology'   => ($cs['technology'] || $tags) ? 'Technology' : null,
    'architecture' => $arch ? 'AI Architecture' : null,
    'results'      => $cs['results'] ? 'The Results' : null,
]);
?>
<article class="cs">
    <!-- Hero -->
    <header class="page-hero cs-hero">
        <div class="page-hero__atmos" aria-hidden="true" data-parallax="0.15"></div>
        <div class="grid-bg" aria-hidden="true"></div>
        <div class="container page-hero__inner">
            <?php component('breadcrumb', ['items' => $page['breadcrumbs']]); ?>
            <div class="cs-hero__meta hero-seq" style="--seq:0">
                <span class="tag tag--accent"><?= e($cs['tag_label'] ?: $cs['category_name']) ?></span>
                <span class="cs-hero__industry"><?= icon('globe') ?><?= e($cs['industry']) ?></span>
            </div>
            <h1 class="page-hero__title display-sm hero-seq--lines"><?= split_lines($cs['title']) ?></h1>
            <p class="page-hero__desc lead hero-seq" style="--seq:2"><?= e($cs['excerpt']) ?></p>

            <?php if ($cs['metrics']): ?>
                <dl class="cs-kpis hero-seq" style="--seq:3">
                    <?php foreach ($cs['metrics'] as $m): ?>
                        <div class="cs-kpi"><dt><?= e($m['label']) ?></dt><dd class="text-gradient"><?= e($m['value']) ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <figure class="cs-cover" data-img-reveal>
            <?php if ($cs['hero_image']): ?>
                <?= picture($cs['hero_image'], $cs['title'] . ' — project overview', 'width="1600" height="900" decoding="async" fetchpriority="high"') ?>
            <?php else: ?>
                <?= visual($cs['visual'], $cs['title'] . ' — abstract project visual', 'cs-cover__visual') ?>
            <?php endif; ?>
        </figure>
    </div>

    <!-- Story -->
    <div class="section cs-body">
        <div class="container cs-layout">
            <nav class="cs-toc" aria-label="On this page">
                <p class="cs-toc__label">On this page</p>
                <ol>
                    <?php $n = 0; foreach ($chapters as $id => $label): ?>
                        <li><a href="#<?= $id ?>" data-toc="<?= $id ?>"><span>0<?= ++$n ?></span><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <div class="cs-content">
                <?php $n = 0; foreach ($chapters as $id => $label): $n++; ?>
                    <section class="cs-chapter" id="<?= $id ?>" aria-labelledby="h-<?= $id ?>" data-chapter>
                        <p class="cs-chapter__num" data-reveal>0<?= $n ?></p>
                        <h2 class="cs-chapter__title" id="h-<?= $id ?>" data-lines><?= split_lines($label) ?></h2>

                        <?php if ($id === 'technology'): ?>
                            <div class="prose" data-reveal><?= paragraphs($cs['technology']) ?></div>
                            <?php if ($tags): ?>
                                <ul class="badge-list badge-list--lg" data-reveal aria-label="Technologies used">
                                    <?php foreach ($tags as $tg): ?><li class="badge"><?= e($tg) ?></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                        <?php elseif ($id === 'architecture'): ?>
                            <ol class="arch">
                                <?php foreach ($arch as $i => $line): [$at, $ad] = array_pad(array_map('trim', explode('|', $line, 2)), 2, ''); ?>
                                    <li class="arch__step" data-reveal style="--delay:<?= .08 * $i ?>s">
                                        <span class="arch__num"><?= $i + 1 ?></span>
                                        <div><h3 class="arch__title"><?= e($at) ?></h3><?php if ($ad): ?><p class="arch__text"><?= e($ad) ?></p><?php endif; ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                            <?php if ($cs['visual'] === 'dashboard'): component('dashboard-mock'); endif; ?>

                        <?php else: ?>
                            <div class="prose" data-reveal><?= paragraphs($cs[$id]) ?></div>
                            <?php if ($id === 'results' && $cs['metrics']): ?>
                                <div class="cs-result-cards">
                                    <?php foreach ($cs['metrics'] as $i => $m): ?>
                                        <div class="cs-result" data-reveal style="--delay:<?= .08 * $i ?>s">
                                            <span class="cs-result__v"><?= e($m['value']) ?></span>
                                            <span class="cs-result__l"><?= e($m['label']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>

                <?php if ($cs['images']): ?>
                    <section class="cs-gallery" aria-label="Project gallery">
                        <?php foreach ($cs['images'] as $img): ?>
                            <figure data-img-reveal>
                                <?= picture($img['path'], $img['caption'] ?: $cs['title'] . ' screenshot', 'loading="lazy" decoding="async"') ?>
                                <?php if ($img['caption']): ?><figcaption><?= e($img['caption']) ?></figcaption><?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($t): ?>
        <section class="section section--alt testimonial-section" aria-label="Client testimonial">
            <div class="container">
                <figure class="testimonial" data-reveal>
                    <span class="testimonial__icon" aria-hidden="true"><?= icon('quote') ?></span>
                    <blockquote class="testimonial__quote"><p><?= e($t['quote']) ?></p></blockquote>
                    <figcaption class="testimonial__by">
                        <span class="testimonial__avatar" aria-hidden="true"><?= e(mb_substr($t['name'], 0, 1)) ?></span>
                        <span><strong><?= e($t['name']) ?></strong><span><?= e(trim($t['designation'] . ($t['company'] ? ', ' . $t['company'] : ''), ', ')) ?></span></span>
                    </figcaption>
                </figure>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($next && (int)$next['id'] !== (int)$cs['id']): ?>
        <nav class="next-project" aria-label="Next case study">
            <div class="container">
                <a class="next-project__link" href="<?= path('case-studies/' . $next['slug'] . '/') ?>" data-cursor="view">
                    <span class="next-project__label">Next project</span>
                    <span class="next-project__title"><?= e($next['title']) ?></span>
                    <span class="next-project__arrow" aria-hidden="true"><?= icon('arrow-right') ?></span>
                </a>
            </div>
        </nav>
    <?php endif; ?>
</article>

<?php component('cta', [
    'heading'     => setting('cs_cta_heading', 'Want Similar *Results?*'),
    'description' => setting('cs_cta_description'),
    'primary'     => [setting('cs_cta_label', 'Start Your Project'), '/contact/'],
    'secondary'   => ['More Case Studies', '/case-studies/'],
]); ?>
