<?php
/** Case study editor — content, metrics repeater, testimonial, gallery and SEO. */
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
require __DIR__ . '/_inc/form.php';
$me = require_admin();

$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$cats = ['' => '— None —'];
foreach (DB::all('SELECT id, name FROM case_study_categories ORDER BY sort_order, id') as $c) {
    $cats[$c['id']] = $c['name'];
}

$fields = [
    'title'       => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 200],
    'slug'        => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'title', 'max' => 200, 'help' => 'Used in /case-studies/{slug}/'],
    'category_id' => ['type' => 'select', 'label' => 'Category', 'options' => $cats],
    'tag_label'   => ['type' => 'text', 'label' => 'Tag label', 'max' => 120, 'help' => 'Shown on cards, e.g. “AI Agents / LLM”.'],
    'industry'    => ['type' => 'text', 'label' => 'Industry / client type', 'max' => 150],
    'visual'      => ['type' => 'select', 'label' => 'Abstract visual (used when no hero image)', 'required' => true, 'options' => visual_names()],
    'hero_image'  => ['type' => 'image', 'label' => 'Hero image'],
    'excerpt'     => ['type' => 'textarea', 'label' => 'Short description', 'rows' => 3, 'max' => 600, 'required' => true],
    'challenge'   => ['type' => 'textarea', 'label' => 'The challenge', 'rows' => 6, 'max' => 8000],
    'solution'    => ['type' => 'textarea', 'label' => 'Our solution', 'rows' => 6, 'max' => 8000],
    'technology'  => ['type' => 'textarea', 'label' => 'Technology (narrative)', 'rows' => 4, 'max' => 4000],
    'tech_tags'   => ['type' => 'list', 'label' => 'Technology tags (one per line)'],
    'architecture'=> ['type' => 'textarea', 'label' => 'AI architecture steps', 'rows' => 5, 'max' => 4000, 'help' => 'One step per line: “Step title | short description”. Leave empty to hide the section.'],
    'results'     => ['type' => 'textarea', 'label' => 'The results', 'rows' => 5, 'max' => 8000],
    'seo_title'   => ['type' => 'text', 'label' => 'SEO title', 'max' => 200, 'counter' => 60],
    'seo_description' => ['type' => 'textarea', 'label' => 'SEO description', 'rows' => 2, 'max' => 300, 'counter' => 160],
    'og_image'    => ['type' => 'image', 'label' => 'Social share image (1200×630)'],
    'is_published'=> ['type' => 'checkbox', 'label' => 'Published', 'default' => 1],
    'is_featured' => ['type' => 'checkbox', 'label' => 'Featured on the home page'],
    'sort_order'  => ['type' => 'number', 'label' => 'Sort order', 'default' => 0],
];

$errors = [];
$row = $id ? DB::one('SELECT * FROM case_studies WHERE id = ?', [$id]) : [];
if ($id && !$row) {
    flash('err', 'Case study not found.');
    admin_redirect('case-studies.php');
}
$metrics = $id ? DB::all('SELECT * FROM case_study_metrics WHERE case_study_id = ? ORDER BY sort_order, id', [$id]) : [];
$testi   = $id ? (DB::one('SELECT * FROM testimonials WHERE case_study_id = ? ORDER BY sort_order, id LIMIT 1', [$id]) ?: []) : [];
$gallery = $id ? DB::all('SELECT * FROM case_study_images WHERE case_study_id = ? ORDER BY sort_order, id', [$id]) : [];

if (is_post()) {
    require_csrf();

    // Remove a gallery image
    if (isset($_POST['remove_image'])) {
        DB::run('DELETE FROM case_study_images WHERE id = ? AND case_study_id = ?', [(int)$_POST['remove_image'], $id]);
        flash('ok', 'Image removed from gallery.');
        admin_redirect('case-study.php?id=' . $id . '#gallery');
    }

    [$data, $errors] = collect_fields($fields, $me['id']);
    $data['category_id'] = $data['category_id'] === '' ? null : (int)$data['category_id'];
    if ($data['slug'] && DB::value('SELECT id FROM case_studies WHERE slug = ? AND id <> ?', [$data['slug'], $id])) {
        $errors['slug'] = 'Another case study already uses this slug.';
    }

    // Metrics repeater
    $mLabels = (array)($_POST['m_label'] ?? []);
    $mValues = (array)($_POST['m_value'] ?? []);
    $newMetrics = [];
    foreach ($mLabels as $i => $l) {
        $l = mb_substr(trim((string)$l), 0, 120);
        $v = mb_substr(trim((string)($mValues[$i] ?? '')), 0, 40);
        if ($l === '' && $v === '') continue;
        if ($l === '' || $v === '') { $errors['metrics'] = 'Each metric needs both a value and a label.'; }
        $newMetrics[] = ['label' => $l, 'value' => $v];
    }

    // Testimonial
    $t = [
        'quote'       => mb_substr(trim((string)($_POST['t_quote'] ?? '')), 0, 2000),
        'name'        => mb_substr(trim((string)($_POST['t_name'] ?? '')), 0, 120),
        'designation' => mb_substr(trim((string)($_POST['t_designation'] ?? '')), 0, 150),
        'company'     => mb_substr(trim((string)($_POST['t_company'] ?? '')), 0, 150),
    ];
    if (($t['quote'] === '') !== ($t['name'] === '')) {
        $errors['testimonial'] = 'A testimonial needs both a quote and a client name.';
    }

    if (!$errors) {
        $pdo = DB::pdo();
        $origId = $id;
        $pdo->beginTransaction();
        try {
            if ($data['is_published'] && empty($row['published_at'])) {
                $data['published_at'] = now();
            }
            if ($id) {
                $data['updated_at'] = now();
                DB::update('case_studies', $data, $id);
            } else {
                $data['created_at'] = now();
                $id = DB::insert('case_studies', $data);
            }
            DB::run('DELETE FROM case_study_metrics WHERE case_study_id = ?', [$id]);
            foreach ($newMetrics as $i => $m) {
                DB::insert('case_study_metrics', $m + ['case_study_id' => $id, 'sort_order' => $i + 1]);
            }
            $existing = DB::value('SELECT id FROM testimonials WHERE case_study_id = ? ORDER BY sort_order, id LIMIT 1', [$id]);
            if ($t['quote'] !== '') {
                $existing ? DB::update('testimonials', $t, (int)$existing) : DB::insert('testimonials', $t + ['case_study_id' => $id, 'is_active' => 1]);
            } elseif ($existing) {
                DB::delete('testimonials', (int)$existing);
            }
            // Gallery uploads (multiple)
            $files = $_FILES['gallery'] ?? null;
            if ($files && is_array($files['name'])) {
                $next = (int)DB::value('SELECT COALESCE(MAX(sort_order), 0) FROM case_study_images WHERE case_study_id = ?', [$id]);
                foreach ($files['name'] as $i => $n) {
                    if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                    $one = ['name' => $n, 'type' => $files['type'][$i], 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]];
                    $up = handle_upload($one, $me['id'], $data['title']);
                    DB::insert('case_study_images', ['case_study_id' => $id, 'path' => $up['path'], 'caption' => null, 'sort_order' => ++$next]);
                }
            }
            // Gallery captions
            foreach ((array)($_POST['g_caption'] ?? []) as $gid => $cap) {
                DB::run('UPDATE case_study_images SET caption = ? WHERE id = ? AND case_study_id = ?', [mb_substr(trim((string)$cap), 0, 255) ?: null, (int)$gid, $id]);
            }
            $pdo->commit();
            flash('ok', 'Case study saved.');
            admin_redirect('case-study.php?id=' . $id);
        } catch (RuntimeException $e) {
            $pdo->rollBack();
            $id = $origId;
            $errors['gallery'] = $e->getMessage();
        }
    }
    $row = array_merge($row, $data);
    $metrics = $newMetrics;
    $testi = $t;
}

if (!$metrics) {
    $metrics = [['label' => '', 'value' => '']];
}

$title  = $id ? 'Edit case study' : 'New case study';
$active = 'case-studies';
require __DIR__ . '/_inc/layout-top.php';
?>
<div class="a-head">
    <a class="a-back" href="<?= admin_url('case-studies.php') ?>"><?= icon('arrow-left') ?>All case studies</a>
    <?php if ($id && !empty($row['is_published'])): ?><a class="a-btn a-btn--ghost a-btn--sm" href="<?= path('case-studies/' . $row['slug'] . '/') ?>" target="_blank" rel="noopener"><?= icon('eye') ?>View live</a><?php endif; ?>
</div>
<?php if ($errors): ?><div class="a-alert a-alert--err" role="alert"><?= icon('alert') ?>Please fix the highlighted fields.<?= isset($errors['metrics']) ? ' ' . e($errors['metrics']) : '' ?><?= isset($errors['testimonial']) ? ' ' . e($errors['testimonial']) : '' ?><?= isset($errors['gallery']) ? ' Gallery: ' . e($errors['gallery']) : '' ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" novalidate class="a-editor" data-dirty-guard>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$id ?>">

    <div class="a-editor__main">
        <section class="a-card a-form-grid">
            <h2 class="a-card__title">Overview</h2>
            <?php foreach (['title', 'slug', 'category_id', 'tag_label', 'industry', 'excerpt'] as $f) echo render_field($f, $fields[$f], $row[$f] ?? null, $errors[$f] ?? null); ?>
        </section>

        <section class="a-card">
            <h2 class="a-card__title">Key metrics</h2>
            <p class="a-muted">Shown in the hero, on cards (first three) and in the results section.</p>
            <div class="a-repeater" data-repeater>
                <?php foreach ($metrics as $m): ?>
                    <div class="a-repeater__row" data-repeater-row>
                        <label><span class="sr-only">Value</span><input name="m_value[]" value="<?= e($m['value']) ?>" placeholder="Value, e.g. 80%" maxlength="40"></label>
                        <label><span class="sr-only">Label</span><input name="m_label[]" value="<?= e($m['label']) ?>" placeholder="Label, e.g. Resolution Rate" maxlength="120"></label>
                        <button type="button" class="a-icon-btn a-icon-btn--danger" data-repeater-remove aria-label="Remove metric"><?= icon('x') ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="a-btn a-btn--ghost a-btn--sm" data-repeater-add><?= icon('plus') ?>Add metric</button>
        </section>

        <section class="a-card a-form-grid">
            <h2 class="a-card__title">Story</h2>
            <?php foreach (['challenge', 'solution', 'technology', 'tech_tags', 'architecture', 'results'] as $f) echo render_field($f, $fields[$f], $row[$f] ?? null, $errors[$f] ?? null); ?>
        </section>

        <section class="a-card a-form-grid">
            <h2 class="a-card__title">Testimonial</h2>
            <div class="a-field a-field--wide"><label for="t_quote">Quote</label><textarea id="t_quote" name="t_quote" rows="3" maxlength="2000"><?= e($testi['quote'] ?? '') ?></textarea></div>
            <div class="a-field"><label for="t_name">Client name</label><input id="t_name" name="t_name" value="<?= e($testi['name'] ?? '') ?>" maxlength="120"></div>
            <div class="a-field"><label for="t_designation">Client designation</label><input id="t_designation" name="t_designation" value="<?= e($testi['designation'] ?? '') ?>" maxlength="150"></div>
            <div class="a-field"><label for="t_company">Company</label><input id="t_company" name="t_company" value="<?= e($testi['company'] ?? '') ?>" maxlength="150"></div>
        </section>

        <section class="a-card" id="gallery">
            <h2 class="a-card__title">Gallery</h2>
            <?php if ($gallery): ?>
                <div class="a-gallery">
                    <?php foreach ($gallery as $g): ?>
                        <figure class="a-gallery__item">
                            <img src="<?= e(upload_url($g['path'])) ?>" alt="">
                            <label><span class="sr-only">Caption</span><input name="g_caption[<?= (int)$g['id'] ?>]" value="<?= e($g['caption']) ?>" placeholder="Caption / alt text"></label>
                            <button class="a-btn a-btn--danger a-btn--sm" type="submit" name="remove_image" value="<?= (int)$g['id'] ?>" formnovalidate><?= icon('trash') ?>Remove</button>
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <label class="a-btn a-btn--ghost a-btn--sm a-file"><?= icon('upload') ?>Add images<input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif"></label>
            <small class="a-help">JPG, PNG, WebP, GIF or AVIF · max <?= round(config('upload_max_bytes') / 1048576) ?> MB each. Saved when you press Save.</small>
        </section>
    </div>

    <aside class="a-editor__side">
        <section class="a-card a-sticky">
            <h2 class="a-card__title">Publish</h2>
            <?php foreach (['is_published', 'is_featured', 'sort_order'] as $f) echo render_field($f, $fields[$f], $row[$f] ?? null, $errors[$f] ?? null); ?>
            <button class="a-btn a-btn--primary a-btn--block" type="submit"><?= icon('check') ?>Save case study</button>
        </section>
        <section class="a-card a-form-grid a-form-grid--single">
            <h2 class="a-card__title">Media</h2>
            <?php foreach (['visual', 'hero_image'] as $f) echo render_field($f, $fields[$f], $row[$f] ?? null, $errors[$f] ?? null); ?>
        </section>
        <section class="a-card a-form-grid a-form-grid--single">
            <h2 class="a-card__title">SEO</h2>
            <?php foreach (['seo_title', 'seo_description', 'og_image'] as $f) echo render_field($f, $fields[$f], $row[$f] ?? null, $errors[$f] ?? null); ?>
        </section>
    </aside>
</form>

<template id="metric-row">
    <div class="a-repeater__row" data-repeater-row>
        <label><span class="sr-only">Value</span><input name="m_value[]" placeholder="Value, e.g. 80%" maxlength="40"></label>
        <label><span class="sr-only">Label</span><input name="m_label[]" placeholder="Label, e.g. Resolution Rate" maxlength="120"></label>
        <button type="button" class="a-icon-btn a-icon-btn--danger" data-repeater-remove aria-label="Remove metric"><?= icon('x') ?></button>
    </div>
</template>

<?php if ($id): ?>
    <form method="post" action="<?= admin_url('case-studies.php') ?>" class="a-danger" data-confirm="Delete this case study permanently?">
        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$id ?>">
        <button class="a-btn a-btn--danger a-btn--sm"><?= icon('trash') ?>Delete case study</button>
    </form>
<?php endif;
require __DIR__ . '/_inc/layout-bottom.php';
