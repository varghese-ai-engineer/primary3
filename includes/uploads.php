<?php
/**
 * Secure image uploads for the admin.
 * - real MIME detection (finfo) against a whitelist, never the client-supplied type
 * - getimagesize() sanity check + dimension limit
 * - size limit from config, random file names, date-based folders
 * - GD re-encode (strips metadata/payloads) and a WebP sibling when supported
 * SVG is intentionally NOT accepted (it can carry script).
 */
declare(strict_types=1);

function handle_upload(array $file, ?int $userId = null, string $alt = ''): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid upload.');
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('No file selected.');
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('File is larger than the server allows.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Upload failed. Please try again.');
    }
    $max = (int)config('upload_max_bytes');
    if ($file['size'] <= 0 || $file['size'] > $max) {
        throw new RuntimeException('File must be smaller than ' . round($max / 1048576) . ' MB.');
    }

    $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
    $allowed = config('upload_mimes');
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WebP, GIF or AVIF images are allowed.');
    }
    $info = @getimagesize($file['tmp_name']);
    if ($info === false || $info[0] < 1 || $info[1] < 1 || $info[0] > 8000 || $info[1] > 8000) {
        throw new RuntimeException('The file is not a valid image (max 8000×8000 px).');
    }

    $ext     = $allowed[$mime];
    $relDir  = 'uploads/' . date('Y/m');
    $absDir  = root_path($relDir);
    if (!is_dir($absDir) && !mkdir($absDir, 0755, true) && !is_dir($absDir)) {
        throw new RuntimeException('Upload folder is not writable.');
    }
    $base    = bin2hex(random_bytes(8));
    $rel     = "$relDir/$base.$ext";
    $abs     = root_path($rel);

    if (!move_uploaded_file($file['tmp_name'], $abs)) {
        throw new RuntimeException('Could not store the file.');
    }
    @chmod($abs, 0644);

    // Re-encode JPEG/PNG with GD (strips EXIF & embedded payloads) and create WebP.
    $webpRel = null;
    if (extension_loaded('gd') && in_array($mime, ['image/jpeg', 'image/png'], true)) {
        try {
            $img = $mime === 'image/jpeg' ? @imagecreatefromjpeg($abs) : @imagecreatefrompng($abs);
            if ($img) {
                if ($mime === 'image/jpeg') {
                    imagejpeg($img, $abs, 85);
                } else {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    imagepng($img, $abs, 8);
                }
                if (function_exists('imagewebp')) {
                    $webpRel = "$relDir/$base.webp";
                    imagewebp($img, root_path($webpRel), 80);
                }
                imagedestroy($img);
            }
        } catch (Throwable $e) {
            log_error('Image re-encode failed: ' . $e->getMessage());
        }
    }

    $id = DB::insert('media', [
        'filename'    => mb_substr(basename((string)$file['name']), 0, 255),
        'path'        => $rel,
        'webp_path'   => $webpRel,
        'mime'        => $mime,
        'size_bytes'  => (int)filesize($abs),
        'width'       => $info[0],
        'height'      => $info[1],
        'alt'         => mb_substr($alt, 0, 255),
        'uploaded_by' => $userId,
        'created_at'  => now(),
    ]);
    return ['id' => $id, 'path' => $rel];
}

/** Validate a stored image reference (uploaded/asset path or absolute https URL). */
function valid_image_ref(string $v): bool
{
    return $v === ''
        || (bool)preg_match('#^(uploads|assets)/[A-Za-z0-9/_\.-]+\.(jpe?g|png|webp|gif|avif|svg)$#', $v) && !str_contains($v, '..')
        || (bool)filter_var($v, FILTER_VALIDATE_URL) && str_starts_with($v, 'https://');
}

/** <picture> with WebP source when a sibling exists. */
function picture(string $path, string $alt, string $attrs = ''): string
{
    $src  = e(upload_url($path));
    $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    $out  = '<picture>';
    if ($webp !== $path && is_file(root_path($webp))) {
        $out .= '<source srcset="' . e(upload_url($webp)) . '" type="image/webp">';
    }
    return $out . '<img src="' . $src . '" alt="' . e($alt) . '" ' . $attrs . '></picture>';
}
