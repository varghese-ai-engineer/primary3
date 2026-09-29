<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

if (is_post()) {
    require_csrf();
    $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
    $act = (string)($_POST['bulk'] ?? '');
    if ($ids && in_array($act, ['read', 'new', 'contacted', 'delete'], true)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        if ($act === 'delete') {
            DB::run("DELETE FROM contact_submissions WHERE id IN ($in)", $ids);
        } else {
            DB::run("UPDATE contact_submissions SET status = ?, updated_at = ? WHERE id IN ($in)", array_merge([$act, now()], $ids));
        }
        flash('ok', count($ids) . ' message' . (count($ids) === 1 ? '' : 's') . ' updated.');
    }
    admin_redirect('messages.php' . (!empty($_POST['qs']) ? '?' . preg_replace('/[^\w=&%\-]/', '', (string)$_POST['qs']) : ''));
}

$status = in_array($_GET['status'] ?? '', ['new', 'read', 'contacted'], true) ? $_GET['status'] : '';
$q      = trim((string)($_GET['q'] ?? ''));
$page   = max(1, (int)($_GET['p'] ?? 1));
$per    = 20;

$where  = [];
$params = [];
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($q !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
$w     = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total = (int)DB::value("SELECT COUNT(*) FROM contact_submissions $w", $params);
$pages = max(1, (int)ceil($total / $per));
$page  = min($page, $pages);
$rows  = DB::all("SELECT * FROM contact_submissions $w ORDER BY created_at DESC, id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
$counts = [];
foreach (DB::all('SELECT status, COUNT(*) AS n FROM contact_submissions GROUP BY status') as $c) $counts[$c['status']] = (int)$c['n'];

$title  = 'Contact Messages';
$active = 'messages';
require __DIR__ . '/_inc/layout-top.php';
$qs = http_build_query(array_filter(['status' => $status, 'q' => $q, 'p' => $page > 1 ? $page : null]));
?>
<div class="a-tabs" role="tablist">
    <?php foreach (['' => 'All', 'new' => 'New', 'read' => 'Read', 'contacted' => 'Contacted'] as $k => $l): ?>
        <a class="a-tab<?= $status === $k ? ' is-active' : '' ?>" href="<?= admin_url('messages.php' . ($k ? '?status=' . $k : '')) ?>"><?= $l ?> <span><?= $k ? ($counts[$k] ?? 0) : array_sum($counts) ?></span></a>
    <?php endforeach; ?>
</div>

<form class="a-toolbar" method="get">
    <?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <label class="a-search"><?= icon('search') ?><span class="sr-only">Search messages</span><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, email, company, message…"></label>
    <button class="a-btn a-btn--ghost a-btn--sm">Search</button>
</form>

<?php if (!$rows): ?>
    <div class="a-card a-empty"><?= icon('inbox') ?><p><?= $q || $status ? 'No messages match your filters.' : 'No enquiries yet. New contact-form submissions will appear here.' ?></p></div>
<?php else: ?>
<form method="post" data-bulk-form>
    <?= csrf_field() ?>
    <input type="hidden" name="qs" value="<?= e($qs) ?>">
    <div class="a-bulk">
        <label class="sr-only" for="bulk">Bulk action</label>
        <select name="bulk" id="bulk">
            <option value="">Bulk action…</option>
            <option value="read">Mark as read</option>
            <option value="new">Mark as unread</option>
            <option value="contacted">Mark as contacted</option>
            <option value="delete">Delete</option>
        </select>
        <button class="a-btn a-btn--ghost a-btn--sm" data-bulk-apply>Apply</button>
    </div>
    <div class="a-card a-table-wrap">
        <table class="a-table">
            <thead><tr>
                <th class="a-col-check"><input type="checkbox" data-check-all aria-label="Select all"></th>
                <th scope="col">From</th><th scope="col">Company</th><th scope="col">Budget</th><th scope="col">Message</th><th scope="col">Status</th><th scope="col">Received</th>
            </tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="<?= $r['status'] === 'new' ? 'is-unread' : '' ?>">
                    <td class="a-col-check"><input type="checkbox" name="ids[]" value="<?= (int)$r['id'] ?>" aria-label="Select message from <?= e($r['name']) ?>"></td>
                    <td data-label="From"><a class="a-strong" href="<?= admin_url('message.php?id=' . (int)$r['id']) ?>"><?= e($r['name']) ?></a><br><small class="a-muted"><?= e($r['email']) ?></small></td>
                    <td data-label="Company"><?= e($r['company'] ?: '—') ?></td>
                    <td data-label="Budget"><?= e($r['budget'] ?: '—') ?></td>
                    <td data-label="Message" class="a-col-msg"><?= e(excerpt($r['message'], 90)) ?></td>
                    <td data-label="Status"><?= status_badge($r['status']) ?></td>
                    <td data-label="Received"><span title="<?= e($r['created_at']) ?>"><?= e(time_ago($r['created_at'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
<?php if ($pages > 1): ?>
    <nav class="a-pager" aria-label="Pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'is-active' : '' ?>" href="<?= admin_url('messages.php?' . http_build_query(array_filter(['status' => $status, 'q' => $q, 'p' => $i]))) ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
<?php endif;
require __DIR__ . '/_inc/layout-bottom.php';
