<?php
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$msg = DB::one('SELECT * FROM contact_submissions WHERE id = ?', [$id]);
if (!$msg) {
    flash('err', 'Message not found.');
    admin_redirect('messages.php');
}

if (is_post()) {
    require_csrf();
    $act = (string)($_POST['action'] ?? '');
    if ($act === 'delete') {
        DB::delete('contact_submissions', $id);
        flash('ok', 'Message deleted.');
        admin_redirect('messages.php');
    }
    if ($act === 'save') {
        $status = in_array($_POST['status'] ?? '', ['new', 'read', 'contacted'], true) ? $_POST['status'] : 'read';
        $notes  = mb_substr(trim((string)($_POST['notes'] ?? '')), 0, 5000);
        DB::update('contact_submissions', ['status' => $status, 'notes' => $notes, 'updated_at' => now()], $id);
        flash('ok', 'Message updated.');
    }
    admin_redirect('message.php?id=' . $id);
}

// Opening an unread message marks it read.
if ($msg['status'] === 'new') {
    DB::update('contact_submissions', ['status' => 'read', 'updated_at' => now()], $id);
    $msg['status'] = 'read';
}

$prev = DB::value('SELECT id FROM contact_submissions WHERE id > ? ORDER BY id ASC LIMIT 1', [$id]);
$next = DB::value('SELECT id FROM contact_submissions WHERE id < ? ORDER BY id DESC LIMIT 1', [$id]);

$title  = 'Enquiry from ' . $msg['name'];
$active = 'messages';
require __DIR__ . '/_inc/layout-top.php';
$mailto = 'mailto:' . rawurlencode($msg['email']) . '?subject=' . rawurlencode('Re: your project enquiry — ' . setting('site_name'));
?>
<div class="a-head">
    <a class="a-back" href="<?= admin_url('messages.php') ?>"><?= icon('arrow-left') ?>All messages</a>
    <div class="a-row">
        <?php if ($prev): ?><a class="a-btn a-btn--ghost a-btn--sm" href="<?= admin_url('message.php?id=' . (int)$prev) ?>">← Newer</a><?php endif; ?>
        <?php if ($next): ?><a class="a-btn a-btn--ghost a-btn--sm" href="<?= admin_url('message.php?id=' . (int)$next) ?>">Older →</a><?php endif; ?>
    </div>
</div>

<div class="a-editor">
    <div class="a-editor__main">
        <article class="a-card">
            <div class="a-msg-head">
                <span class="a-avatar"><?= e(mb_strtoupper(mb_substr($msg['name'], 0, 1))) ?></span>
                <div>
                    <h2 class="a-card__title"><?= e($msg['name']) ?></h2>
                    <p class="a-muted"><a href="<?= e($mailto) ?>"><?= e($msg['email']) ?></a><?= $msg['phone'] ? ' · <a href="tel:' . e(preg_replace('/[^\d+]/', '', $msg['phone'])) . '">' . e($msg['phone']) . '</a>' : '' ?></p>
                </div>
                <?= status_badge($msg['status']) ?>
            </div>
            <dl class="a-dl">
                <div><dt>Company</dt><dd><?= e($msg['company'] ?: '—') ?></dd></div>
                <div><dt>Budget</dt><dd><?= e($msg['budget'] ?: '—') ?></dd></div>
                <div><dt>Received</dt><dd><?= e(date('M j, Y · H:i', strtotime($msg['created_at']))) ?></dd></div>
                <div><dt>IP</dt><dd><?= e($msg['ip'] ?: '—') ?></dd></div>
            </dl>
            <div class="a-msg-body"><?= nl2br(e($msg['message'])) ?></div>
            <a class="a-btn a-btn--primary" href="<?= e($mailto) ?>"><?= icon('mail') ?>Reply by email</a>
        </article>
    </div>
    <aside class="a-editor__side">
        <form class="a-card a-form-grid a-form-grid--single" method="post">
            <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$id ?>">
            <h2 class="a-card__title">Status &amp; notes</h2>
            <div class="a-field"><label for="st">Status</label>
                <select id="st" name="status">
                    <?php foreach (['new' => 'Unread', 'read' => 'Read', 'contacted' => 'Contacted'] as $k => $l): ?>
                        <option value="<?= $k ?>"<?= $msg['status'] === $k ? ' selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="a-field"><label for="notes">Internal notes</label><textarea id="notes" name="notes" rows="6" maxlength="5000" placeholder="Visible to admins only"><?= e($msg['notes']) ?></textarea></div>
            <button class="a-btn a-btn--primary a-btn--block"><?= icon('check') ?>Save</button>
        </form>
        <form method="post" class="a-danger" data-confirm="Delete this enquiry permanently?">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$id ?>">
            <button class="a-btn a-btn--danger a-btn--sm"><?= icon('trash') ?>Delete enquiry</button>
        </form>
    </aside>
</div>
<?php require __DIR__ . '/_inc/layout-bottom.php';
