<?php
/** Media library — upload, browse, edit alt text, copy path, delete. */
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

if (is_post()) {
    require_csrf();
    $act = (string)($_POST['action'] ?? '');
    if ($act === 'upload') {
        $files = $_FILES['files'] ?? null;
        $ok = 0;
        $errs = [];
        if ($files && is_array($files['name'])) {
            foreach ($files['name'] as $i => $n) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                try {
                    handle_upload(['name' => $n, 'type' => $files['type'][$i], 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]], $me['id'], pathinfo((string)$n, PATHINFO_FILENAME));
                    $ok++;
                } catch (RuntimeException $e) {
                    $errs[] = basename((string)$n) . ': ' . $e->getMessage();
                }
            }
        }
        if ($ok) flash('ok', "$ok file" . ($ok === 1 ? '' : 's') . ' uploaded.');
        if ($errs) flash('err', implode(' ', $errs));
        if (!$ok && !$errs) flash('err', 'Choose at least one image.');
    } elseif ($act === 'alt') {
        DB::run('UPDATE media SET alt = ? WHERE id = ?', [mb_substr(trim((string)($_POST['alt'] ?? '')), 0, 255), (int)$_POST['id']]);
        flash('ok', 'Alt text saved.');
    } elseif ($act === 'delete') {
        $m = DB::one('SELECT * FROM media WHERE id = ?', [(int)$_POST['id']]);
        if ($m) {
            foreach ([$m['path'], $m['webp_path']] as $p) {
                // Only ever delete inside /uploads.
                if ($p && str_starts_with($p, 'uploads/') && !str_contains($p, '..') && is_file(root_path($p))) {
                    @unlink(root_path($p));
                }
            }
            DB::delete('media', (int)$m['id']);
            flash('ok', 'File deleted. Update any content that referenced it.');
        }
    }
    admin_redirect('media.php');
}

$items = DB::all('SELECT * FROM media ORDER BY id DESC LIMIT 300');
$title  = 'Media Library';
$active = 'media';
require __DIR__ . '/_inc/layout-top.php';
?>
<form class="a-card a-drop" method="post" enctype="multipart/form-data" data-drop>
    <?= csrf_field() ?><input type="hidden" name="action" value="upload">
    <?= icon('upload') ?>
    <p><strong>Drop images here</strong> or choose files</p>
    <small class="a-muted">JPG, PNG, WebP, GIF, AVIF · up to <?= round(config('upload_max_bytes') / 1048576) ?> MB each · WebP copies are generated automatically</small>
    <label class="a-btn a-btn--primary a-btn--sm a-file">Choose files<input type="file" name="files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif" data-drop-input></label>
</form>

<?php if (!$items): ?>
    <div class="a-card a-empty"><?= icon('image') ?><p>No media yet. Uploaded images appear here and can be used in any image field.</p></div>
<?php else: ?>
<div class="a-media-grid">
    <?php foreach ($items as $m): ?>
        <figure class="a-media">
            <div class="a-media__img"><img src="<?= e(upload_url($m['path'])) ?>" alt="<?= e($m['alt']) ?>" loading="lazy"></div>
            <figcaption>
                <p class="a-media__name" title="<?= e($m['filename']) ?>"><?= e($m['filename']) ?></p>
                <p class="a-muted"><?= (int)$m['width'] ?>×<?= (int)$m['height'] ?> · <?= round($m['size_bytes'] / 1024) ?> KB<?= $m['webp_path'] ? ' · WebP' : '' ?></p>
                <div class="a-copy"><input readonly value="<?= e($m['path']) ?>" aria-label="File path"><button type="button" class="a-icon-btn" data-copy title="Copy path" aria-label="Copy path"><?= icon('file-text') ?></button></div>
                <form method="post" class="a-media__alt">
                    <?= csrf_field() ?><input type="hidden" name="action" value="alt"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <label class="sr-only" for="alt<?= (int)$m['id'] ?>">Alt text</label>
                    <input id="alt<?= (int)$m['id'] ?>" name="alt" value="<?= e($m['alt']) ?>" placeholder="Alt text">
                    <button class="a-icon-btn" title="Save alt text" aria-label="Save alt text"><?= icon('check') ?></button>
                </form>
                <form method="post" data-confirm="Delete this file? Pages using it will show no image.">
                    <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <button class="a-link-danger"><?= icon('trash') ?>Delete</button>
                </form>
            </figcaption>
        </figure>
    <?php endforeach; ?>
</div>
<?php endif;
require __DIR__ . '/_inc/layout-bottom.php';
