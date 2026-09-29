<?php
/** Brand lockup. Props: $class */
$name = setting('logo_text', 'Primary');
$sub  = setting('logo_suffix', 'Infotech');
$uid  = 'lg-' . preg_replace('/[^a-z0-9]/i', '', (string)($class ?? 'x'));
?>
<a class="logo <?= e($class ?? '') ?>" href="<?= path('/') ?>" aria-label="<?= e(setting('site_name', 'Primary Infotech')) ?> — home">
    
	<svg class="logo__mark" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="<?= e($uid) ?>" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#A78BFA"/><stop offset=".55" stop-color="#6366F1"/><stop offset="1" stop-color="#22D3EE"/>
            </linearGradient>
        </defs>
        <rect x="1" y="1" width="30" height="30" rx="9" fill="#0D0D0F"/>
        <path d="M16 22.5V9.5v1a2.2 2.2 0 0 1-2.2 2.2H12.7M19.3 22.5H12.7" fill="none" stroke="url(#<?= e($uid) ?>)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="20.3" cy="11" r="1.7" fill="url(#<?= e($uid) ?>)"/>
        <path d="M7.5 8.5c-1.6 0-2.4.8-2.4 2.4v2.6c0 1.4-.8 2.3-2 2.5 1.2.2 2 1.1 2 2.5v2.6c0 1.6.8 2.4 2.4 2.4M24.5 8.5c1.6 0 2.4.8 2.4 2.4v2.6c0 1.4.8 2.3 2 2.5-1.2.2-2 1.1-2 2.5v2.6c0 1.6-.8 2.4-2.4 2.4" fill="none" stroke="url(#<?= e($uid) ?>)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="logo__text"><?= e($name) ?></span>
</a>
