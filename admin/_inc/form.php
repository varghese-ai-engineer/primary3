<?php
/**
 * Form rendering + input collection/validation for declarative fields.
 */
declare(strict_types=1);

function media_paths(): array
{
    return memo('media_paths', fn() => array_column(DB::all('SELECT path FROM media ORDER BY id DESC LIMIT 200'), 'path'));
}

function render_field(string $name, array $f, $value, ?string $error = null): string
{
    $id    = 'f_' . $name;
    $req   = !empty($f['required']);
    $label = e($f['label'] ?? ucfirst($name)) . ($req ? ' <span class="a-req" aria-hidden="true">*</span>' : '');
    $help  = !empty($f['help']) ? '<small class="a-help" id="' . $id . '_h">' . e($f['help']) . '</small>' : '';
    $desc  = trim((!empty($f['help']) ? $id . '_h ' : '') . ($error ? $id . '_e' : ''));
    $aria  = ($desc ? ' aria-describedby="' . $desc . '"' : '') . ($error ? ' aria-invalid="true"' : '') . ($req ? ' required' : '');
    $max   = isset($f['max']) && in_array($f['type'], ['text', 'textarea', 'url', 'email', 'slug'], true) ? ' maxlength="' . (int)$f['max'] . '"' : '';
    $cnt   = !empty($f['counter']) ? ' data-counter="' . (int)$f['counter'] . '"' : '';
    $err   = $error ? '<small class="a-error" id="' . $id . '_e">' . e($error) . '</small>' : '';
    $v     = $value ?? ($f['default'] ?? '');

    switch ($f['type']) {
        case 'textarea':
            $input = '<textarea id="' . $id . '" name="' . $name . '" rows="' . (int)($f['rows'] ?? 4) . '"' . $max . $aria . $cnt . '>' . e($v) . '</textarea>';
            break;
        case 'list':
            $lines = implode("\n", json_list($v));
            $input = '<textarea id="' . $id . '" name="' . $name . '" rows="4"' . $aria . '>' . e($lines) . '</textarea>';
            break;
        case 'number':
            $input = '<input type="number" step="1" id="' . $id . '" name="' . $name . '" value="' . e($v) . '"' . (isset($f['min']) ? ' min="' . (int)$f['min'] . '"' : '') . (isset($f['max']) ? ' max="' . (int)$f['max'] . '"' : '') . $aria . '>';
            break;
        case 'decimal':
            $input = '<input type="number" step="any" id="' . $id . '" name="' . $name . '" value="' . e($v) . '"' . $aria . '>';
            break;
        case 'select':
        case 'icon':
        case 'relation':
            $opts = $f['options'] ?? [];
            if ($f['type'] === 'relation') {
                $opts = ['' => '— None —'];
                foreach (DB::all($f['query']) as $r) {
                    $opts[$r['id']] = $r['label'];
                }
            } elseif (!$req) {
                $opts = ['' => '— Select —'] + $opts;
            }
            $input = '<select id="' . $id . '" name="' . $name . '"' . $aria . ($f['type'] === 'icon' ? ' data-icon-select' : '') . '>';
            foreach ($opts as $k => $l) {
                $input .= '<option value="' . e($k) . '"' . ((string)$k === (string)$v ? ' selected' : '') . '>' . e($l) . '</option>';
            }
            $input .= '</select>';
            if ($f['type'] === 'icon') {
                static $sprites = false;
                if (!$sprites) {       // one hidden copy of each icon so JS can swap the preview
                    $sprites = true;
                    $input .= '<div hidden data-icon-sprites>' . implode('', array_map(fn($n) => '<span data-icon="' . e($n) . '">' . icon($n) . '</span>', icon_names())) . '</div>';
                }
                $input = '<div class="a-icon-pick"><span class="a-icon-preview" data-icon-preview>' . icon((string)$v ?: 'sparkles') . '</span>' . $input . '</div>';
            }
            break;
        case 'checkbox':
            return '<div class="a-field a-field--check"><label class="a-check"><input type="hidden" name="' . $name . '" value="0"><input type="checkbox" id="' . $id . '" name="' . $name . '" value="1"' . ((int)$v ? ' checked' : '') . '><span>' . e($f['label']) . '</span></label>' . $help . $err . '</div>';
        case 'image':
            $list  = 'dl_' . $name;
            $prev  = $v ? '<img src="' . e(upload_url((string)$v)) . '" alt="" data-img-preview>' : '<img alt="" data-img-preview hidden>';
            $input = '<div class="a-image" data-image-field>'
                . '<div class="a-image__preview">' . $prev . '<span class="a-image__empty"' . ($v ? ' hidden' : '') . '>' . icon('image') . '</span></div>'
                . '<div class="a-image__controls">'
                . '<input type="text" id="' . $id . '" name="' . $name . '" value="' . e($v) . '" list="' . $list . '" placeholder="uploads/… or pick from library"' . $aria . ' data-img-path>'
                . '<datalist id="' . $list . '">' . implode('', array_map(fn($p) => '<option value="' . e($p) . '">', media_paths())) . '</datalist>'
                . '<label class="a-btn a-btn--ghost a-btn--sm a-file">' . icon('upload') . 'Upload new<input type="file" name="' . $name . '_file" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" data-img-file></label>'
                . '<button type="button" class="a-btn a-btn--ghost a-btn--sm" data-img-clear>Remove</button>'
                . '</div></div>';
            break;
        case 'url':
            $input = '<input type="url" id="' . $id . '" name="' . $name . '" value="' . e($v) . '" placeholder="https://"' . $max . $aria . '>';
            break;
        case 'email':
            $input = '<input type="email" id="' . $id . '" name="' . $name . '" value="' . e($v) . '"' . $max . $aria . '>';
            break;
        case 'slug':
            $input = '<input type="text" id="' . $id . '" name="' . $name . '" value="' . e($v) . '" pattern="[a-z0-9\-]+"' . $max . $aria . ' data-slug-from="f_' . e($f['from'] ?? 'title') . '" placeholder="auto-generated">';
            break;
        default:
            $input = '<input type="text" id="' . $id . '" name="' . $name . '" value="' . e($v) . '"' . $max . $aria . $cnt . '>';
    }
    return '<div class="a-field' . ($error ? ' has-error' : '') . ($f['type'] === 'textarea' || $f['type'] === 'list' || $f['type'] === 'image' ? ' a-field--wide' : '') . '"><label for="' . $id . '">' . $label . '</label>' . $input . $help . $err . '</div>';
}

/**
 * Collect + validate POSTed values for the given field specs.
 * @return array{0: array, 1: array} [data, errors]
 */
function collect_fields(array $fields, ?int $userId = null): array
{
    $data = [];
    $errors = [];
    foreach ($fields as $name => $f) {
        $raw = $_POST[$name] ?? null;
        $val = is_string($raw) ? trim($raw) : $raw;
        switch ($f['type']) {
            case 'checkbox':
                $val = (int)($val === '1');
                break;
            case 'number':
                if ($val === '' || $val === null) { $val = (int)($f['default'] ?? 0); break; }
                if (!preg_match('/^-?\d+$/', (string)$val)) { $errors[$name] = 'Must be a whole number.'; }
                $val = (int)$val;
                if (isset($f['min']) && $val < $f['min']) $errors[$name] = 'Minimum is ' . $f['min'] . '.';
                if (isset($f['max']) && $val > $f['max']) $errors[$name] = 'Maximum is ' . $f['max'] . '.';
                break;
            case 'decimal':
                if (!is_numeric($val)) { $errors[$name] = 'Must be a number.'; $val = 0; }
                $val = (float)$val;
                break;
            case 'list':
                $val = lines_to_json((string)$val);
                break;
            case 'slug':
                $src = trim((string)($_POST[$f['from'] ?? 'title'] ?? ''));
                $val = slugify($val !== '' && $val !== null ? (string)$val : $src);
                break;
            case 'select':
            case 'icon':
                $val = (string)$val;
                if ($val !== '' && !array_key_exists($val, $f['options'])) { $errors[$name] = 'Invalid choice.'; }
                break;
            case 'relation':
                $val = ($val === '' || $val === null) ? null : (int)$val;
                break;
            case 'url':
                $val = (string)$val;
                if ($val !== '' && (!filter_var($val, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $val))) { $errors[$name] = 'Enter a valid URL starting with https://'; }
                break;
            case 'email':
                $val = (string)$val;
                if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) { $errors[$name] = 'Enter a valid email.'; }
                break;
            case 'image':
                $val = (string)$val;
                $file = $_FILES[$name . '_file'] ?? null;
                if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    try {
                        $val = handle_upload($file, $userId)['path'];
                    } catch (RuntimeException $e) {
                        $errors[$name] = $e->getMessage();
                    }
                } elseif (!valid_image_ref($val)) {
                    $errors[$name] = 'Use an uploaded image path (uploads/…) or an https:// URL.';
                }
                break;
            default:
                $val = (string)$val;
        }
        if (!empty($f['required']) && ($val === '' || $val === null || $val === '[]')) {
            $errors[$name] = ($f['label'] ?? $name) . ' is required.';
        }
        if (isset($f['max']) && is_string($val) && in_array($f['type'], ['text', 'textarea', 'url', 'email', 'slug'], true) && mb_strlen($val) > $f['max']) {
            $errors[$name] = 'Maximum ' . $f['max'] . ' characters.';
        }
        if ($f['type'] !== 'relation' && $val === '' && in_array($f['type'], ['image', 'url', 'text', 'textarea', 'email'], true) && empty($f['required'])) {
            $val = $f['type'] === 'text' || $f['type'] === 'textarea' ? '' : null;
        }
        $data[$name] = $val;
    }
    return [$data, $errors];
}
