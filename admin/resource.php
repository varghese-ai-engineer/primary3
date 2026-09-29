<?php
/**
 * Generic CRUD controller driven by _inc/resources.php
 *   GET  resource.php?r=team                 list
 *   GET  resource.php?r=team&action=edit&id= edit / new
 *   POST action=save|delete|toggle|reorder   (CSRF protected)
 */
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
require __DIR__ . '/_inc/resources.php';
require __DIR__ . '/_inc/form.php';

$me   = require_admin();
$key  = (string)($_GET['r'] ?? $_POST['r'] ?? '');
$all  = admin_resources();
if (!isset($all[$key])) {
    flash('err', 'Unknown section.');
    admin_redirect('index.php');
}
$res    = $all[$key];
$table  = $res['table'];
$action = (string)($_POST['action'] ?? $_GET['action'] ?? 'list');
$id     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$self   = 'resource.php?r=' . $key;

/* ------------------------------ POST actions ------------------------------ */
if (is_post()) {
    require_csrf();

    if ($action === 'delete' && $id) {
        DB::delete($table, $id);
        flash('ok', $res['singular'] . ' deleted.');
        admin_redirect($self);
    }

    if ($action === 'toggle' && $id && !empty($res['toggle'])) {
        $col = $res['toggle'];
        DB::assertIdent($col);
        DB::run("UPDATE $table SET $col = 1 - $col WHERE id = ?", [$id]);
        if (is_ajax()) json_response(['ok' => true]);
        admin_redirect($self);
    }

    if ($action === 'reorder' && !empty($res['sortable'])) {
        $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
        $pdo = DB::pdo();
        $pdo->beginTransaction();
        foreach ($ids as $i => $rid) {
            DB::run("UPDATE $table SET sort_order = ? WHERE id = ?", [$i + 1, $rid]);
        }
        $pdo->commit();
        json_response(['ok' => true]);
    }

    if ($action === 'save') {
        [$data, $errors] = collect_fields($res['fields'], $me['id']);
        foreach ($res['unique'] ?? [] as $col) {
            DB::assertIdent($col);
            if (isset($data[$col]) && DB::value("SELECT id FROM $table WHERE $col = ? AND id <> ?", [$data[$col], $id])) {
                $errors[$col] = 'This value is already used.';
            }
        }
        if (!$errors) {
            if (in_array($table, ['services'], true)) {
                $data[$id ? 'updated_at' : 'created_at'] = now();
            }
            if ($id) {
                DB::update($table, $data, $id);
            } else {
                $id = DB::insert($table, $data);
            }
            flash('ok', $res['singular'] . ' saved.');
            admin_redirect(isset($_POST['save_new']) ? $self . '&action=edit' : $self . '&action=edit&id=' . $id);
        }
        $row = $data;
        $action = 'edit';
    }
}

/* ------------------------------ Edit screen ------------------------------ */
if ($action === 'edit') {
    if (!isset($row)) {
        $row = $id ? DB::one("SELECT * FROM $table WHERE id = ?", [$id]) : [];
        if ($id && !$row) {
            flash('err', 'Item not found.');
            admin_redirect($self);
        }
    }
    $errors = $errors ?? [];
    $title  = ($id ? 'Edit ' : 'New ') . strtolower($res['singular']);
    $active = $key;
    require __DIR__ . '/_inc/layout-top.php';
    ?>
    <div class="a-head">
        <a class="a-back" href="<?= admin_url($self) ?>"><?= icon('arrow-left') ?><?= e($res['title']) ?></a>
    </div>
    <?php if ($errors): ?><div class="a-alert a-alert--err" role="alert"><?= icon('alert') ?>Please fix the highlighted fields.</div><?php endif; ?>
    <form class="a-card a-form-grid" method="post" enctype="multipart/form-data" novalidate data-dirty-guard>
        <?= csrf_field() ?>
        <input type="hidden" name="r" value="<?= e($key) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <?php foreach ($res['fields'] as $name => $f) echo render_field($name, $f, $row[$name] ?? null, $errors[$name] ?? null); ?>
        <div class="a-actions">
            <button class="a-btn a-btn--primary" type="submit"><?= icon('check') ?>Save</button>
            <button class="a-btn a-btn--ghost" type="submit" name="save_new" value="1">Save &amp; add another</button>
            <a class="a-btn a-btn--ghost" href="<?= admin_url($self) ?>">Cancel</a>
        </div>
    </form>
    <?php if ($id): ?>
        <form method="post" class="a-danger" data-confirm="Delete this <?= e(strtolower($res['singular'])) ?>? This cannot be undone.">
            <?= csrf_field() ?><input type="hidden" name="r" value="<?= e($key) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$id ?>">
            <button class="a-btn a-btn--danger a-btn--sm" type="submit"><?= icon('trash') ?>Delete <?= e(strtolower($res['singular'])) ?></button>
        </form>
    <?php endif;
    require __DIR__ . '/_inc/layout-bottom.php';
    exit;
}

/* ------------------------------ List screen ------------------------------ */
$where  = '';
$params = [];
$filterVal = '';
if (!empty($res['filter'])) {
    [$fcol, $fopts] = $res['filter'];
    $filterVal = (string)($_GET['f'] ?? '');
    if ($filterVal !== '' && isset($fopts[$filterVal])) {
        DB::assertIdent($fcol);
        $where = "WHERE $fcol = ?";
        $params[] = $filterVal;
    } else {
        $filterVal = '';
    }
}
$q = trim((string)($_GET['q'] ?? ''));
$firstCol = array_key_first(array_diff_key($res['columns'], ['photo' => 1]));
if ($q !== '') {
    DB::assertIdent($firstCol);
    $where .= ($where ? ' AND ' : 'WHERE ') . "$firstCol LIKE ?";
    $params[] = '%' . $q . '%';
}
$rows = DB::all("SELECT * FROM $table $where ORDER BY {$res['order']}", $params);

$relLabels = [];
foreach ($res['fields'] as $n => $f) {
    if ($f['type'] === 'relation') {
        foreach (DB::all($f['query']) as $r) $relLabels[$n][$r['id']] = $r['label'];
    }
}

$title  = $res['title'];
$active = $key;
require __DIR__ . '/_inc/layout-top.php';
?>
<div class="a-head">
    <div>
        <p class="a-muted"><?= count($rows) ?> item<?= count($rows) === 1 ? '' : 's' ?><?= !empty($res['sortable']) && !$filterVal && $q === '' ? ' · drag rows to reorder' : '' ?></p>
        <?php if (!empty($res['intro'])): ?><p class="a-intro"><?= e($res['intro']) ?></p><?php endif; ?>
    </div>
    <a class="a-btn a-btn--primary" href="<?= admin_url($self . '&action=edit') ?>"><?= icon('plus') ?>New <?= e(strtolower($res['singular'])) ?></a>
</div>

<form class="a-toolbar" method="get">
    <input type="hidden" name="r" value="<?= e($key) ?>">
    <label class="a-search"><?= icon('search') ?><span class="sr-only">Search</span><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search <?= e(strtolower($res['columns'][$firstCol])) ?>…"></label>
    <?php if (!empty($res['filter'])): ?>
        <label class="sr-only" for="flt">Filter</label>
        <select id="flt" name="f" data-autosubmit>
            <option value="">All</option>
            <?php foreach ($res['filter'][1] as $k => $l): ?><option value="<?= e($k) ?>"<?= $filterVal === (string)$k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
        </select>
    <?php endif; ?>
    <button class="a-btn a-btn--ghost a-btn--sm">Apply</button>
</form>

<?php if (!$rows): ?>
    <div class="a-card a-empty"><?= icon('inbox') ?><p>Nothing here yet.</p><a class="a-btn a-btn--primary a-btn--sm" href="<?= admin_url($self . '&action=edit') ?>">Create the first one</a></div>
<?php else: ?>
<div class="a-card a-table-wrap">
    <table class="a-table">
        <thead><tr>
            <?php if (!empty($res['sortable'])): ?><th class="a-col-drag"><span class="sr-only">Reorder</span></th><?php endif; ?>
            <?php foreach ($res['columns'] as $c => $label): ?><th scope="col"><?= e($label) ?></th><?php endforeach; ?>
            <th class="a-col-actions"><span class="sr-only">Actions</span></th>
        </tr></thead>
        <tbody <?= !empty($res['sortable']) && !$filterVal && $q === '' ? 'data-sortable="' . e(admin_url('resource.php')) . '" data-resource="' . e($key) . '"' : '' ?>>
        <?php foreach ($rows as $r): ?>
            <tr data-id="<?= (int)$r['id'] ?>">
                <?php if (!empty($res['sortable'])): ?><td class="a-col-drag"><span class="a-grip" title="Drag to reorder" aria-hidden="true"><?= icon('grip') ?></span></td><?php endif; ?>
                <?php foreach ($res['columns'] as $c => $label):
                    $v = $r[$c] ?? '';
                    $f = $res['fields'][$c] ?? ['type' => 'text']; ?>
                    <td data-label="<?= e($label) ?>">
                        <?php if ($c === ($res['toggle'] ?? null)): ?>
                            <form method="post" class="a-inline" data-toggle-form>
                                <?= csrf_field() ?><input type="hidden" name="r" value="<?= e($key) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button class="a-switch<?= (int)$v ? ' is-on' : '' ?>" role="switch" aria-checked="<?= (int)$v ? 'true' : 'false' ?>" aria-label="Toggle visibility"><span></span></button>
                            </form>
                        <?php elseif ($f['type'] === 'checkbox'): ?>
                            <?= (int)$v ? '<span class="a-badge a-badge--green">Yes</span>' : '<span class="a-badge a-badge--grey">No</span>' ?>
                        <?php elseif ($f['type'] === 'image'): ?>
                            <?= $v ? '<img class="a-thumb" src="' . e(upload_url($v)) . '" alt="">' : '<span class="a-thumb a-thumb--empty">' . icon('image') . '</span>' ?>
                        <?php elseif ($f['type'] === 'relation'): ?>
                            <?= e($relLabels[$c][$v] ?? '—') ?>
                        <?php elseif ($f['type'] === 'select'): ?>
                            <span class="a-badge a-badge--grey"><?= e($f['options'][$v] ?? $v) ?></span>
                        <?php elseif ($c === $firstCol): ?>
                            <a class="a-strong" href="<?= admin_url($self . '&action=edit&id=' . (int)$r['id']) ?>"><?= e(excerpt((string)$v, 80)) ?></a>
                        <?php else: ?>
                            <?= e(excerpt((string)$v, 60)) ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <td class="a-col-actions">
                    <a class="a-icon-btn" href="<?= admin_url($self . '&action=edit&id=' . (int)$r['id']) ?>" title="Edit" aria-label="Edit"><?= icon('edit') ?></a>
                    <form method="post" class="a-inline" data-confirm="Delete this item? This cannot be undone.">
                        <?= csrf_field() ?><input type="hidden" name="r" value="<?= e($key) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
