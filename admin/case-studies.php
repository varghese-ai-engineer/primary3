<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

if (is_post()) {
    require_csrf();
    $id     = (int)($_POST['id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'delete' && $id) {
        DB::delete('case_studies', $id);           // metrics & gallery cascade; testimonial link set null
        flash('ok', 'Case study deleted.');
    } elseif (in_array($action, ['publish', 'feature'], true) && $id) {
        $col = $action === 'publish' ? 'is_published' : 'is_featured';
        DB::run("UPDATE case_studies SET $col = 1 - $col, updated_at = ? WHERE id = ?", [now(), $id]);
        if ($action === 'publish') {
            DB::run('UPDATE case_studies SET published_at = ? WHERE id = ? AND is_published = 1 AND published_at IS NULL', [now(), $id]);
        }
        if (is_ajax()) json_response(['ok' => true]);
    } elseif ($action === 'reorder') {
        $pdo = DB::pdo();
        $pdo->beginTransaction();
        foreach (array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? [])))) as $i => $rid) {
            DB::run('UPDATE case_studies SET sort_order = ? WHERE id = ?', [$i + 1, $rid]);
        }
        $pdo->commit();
        json_response(['ok' => true]);
    }
    admin_redirect('case-studies.php');
}

$rows = DB::all(
    'SELECT cs.*, c.name AS category_name,
            (SELECT COUNT(*) FROM case_study_metrics m WHERE m.case_study_id = cs.id) AS metric_count
     FROM case_studies cs LEFT JOIN case_study_categories c ON c.id = cs.category_id
     ORDER BY cs.sort_order, cs.id'
);

$title  = 'Case Studies';
$active = 'case-studies';
require __DIR__ . '/_inc/layout-top.php';
?>
<div class="a-head">
    <p class="a-muted"><?= count($rows) ?> case studies · drag rows to reorder</p>
    <a class="a-btn a-btn--primary" href="<?= admin_url('case-study.php') ?>"><?= icon('plus') ?>New case study</a>
</div>

<?php if (!$rows): ?>
    <div class="a-card a-empty"><?= icon('layers') ?><p>No case studies yet.</p><a class="a-btn a-btn--primary a-btn--sm" href="<?= admin_url('case-study.php') ?>">Write the first one</a></div>
<?php else: ?>
<div class="a-card a-table-wrap">
    <table class="a-table">
        <thead><tr>
            <th class="a-col-drag"><span class="sr-only">Reorder</span></th>
            <th scope="col">Title</th><th scope="col">Category</th><th scope="col">Industry</th><th scope="col">Metrics</th>
            <th scope="col">Published</th><th scope="col">Featured</th><th class="a-col-actions"><span class="sr-only">Actions</span></th>
        </tr></thead>
        <tbody data-sortable="<?= e(admin_url('case-studies.php')) ?>">
        <?php foreach ($rows as $r): ?>
            <tr data-id="<?= (int)$r['id'] ?>">
                <td class="a-col-drag"><span class="a-grip" aria-hidden="true"><?= icon('grip') ?></span></td>
                <td data-label="Title"><a class="a-strong" href="<?= admin_url('case-study.php?id=' . (int)$r['id']) ?>"><?= e($r['title']) ?></a><br><small class="a-muted">/case-studies/<?= e($r['slug']) ?>/</small></td>
                <td data-label="Category"><span class="a-badge a-badge--violet"><?= e($r['category_name'] ?: '—') ?></span></td>
                <td data-label="Industry"><?= e($r['industry']) ?></td>
                <td data-label="Metrics"><?= (int)$r['metric_count'] ?></td>
                <?php foreach (['publish' => 'is_published', 'feature' => 'is_featured'] as $act => $col): ?>
                    <td data-label="<?= $act === 'publish' ? 'Published' : 'Featured' ?>">
                        <form method="post" class="a-inline" data-toggle-form>
                            <?= csrf_field() ?><input type="hidden" name="action" value="<?= $act ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button class="a-switch<?= (int)$r[$col] ? ' is-on' : '' ?>" role="switch" aria-checked="<?= (int)$r[$col] ? 'true' : 'false' ?>" aria-label="<?= $act === 'publish' ? 'Published' : 'Featured' ?>"><span></span></button>
                        </form>
                    </td>
                <?php endforeach; ?>
                <td class="a-col-actions">
                    <?php if ($r['is_published']): ?><a class="a-icon-btn" href="<?= path('case-studies/' . $r['slug'] . '/') ?>" target="_blank" rel="noopener" title="View" aria-label="View on site"><?= icon('eye') ?></a><?php endif; ?>
                    <a class="a-icon-btn" href="<?= admin_url('case-study.php?id=' . (int)$r['id']) ?>" title="Edit" aria-label="Edit"><?= icon('edit') ?></a>
                    <form method="post" class="a-inline" data-confirm="Delete “<?= e($r['title']) ?>”? Metrics and gallery images will be removed too.">
                        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="a-icon-btn a-icon-btn--danger" title="Delete" aria-label="Delete"><?= icon('trash') ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif;
require __DIR__ . '/_inc/layout-bottom.php';
