<?php
/**
 * Inline SVG icon set (Lucide-style, 24px grid, stroke-based).
 * Inline SVG = zero extra requests and no icon-font FOUT.
 */
declare(strict_types=1);

function icon_paths(): array
{
    return [
        'arrow-right'   => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'arrow-up-right'=> '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
        'arrow-left'    => '<path d="M19 12H5"/><path d="m11 18-6-6 6-6"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'plus'          => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'minus'         => '<path d="M5 12h14"/>',
        'check'         => '<path d="M20 6 9 17l-5-5"/>',
        'x'             => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'menu'          => '<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/>',
        'bot'           => '<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V4"/><circle cx="12" cy="3" r="1"/><path d="M9 13v2"/><path d="M15 13v2"/><path d="M2 13v3"/><path d="M22 13v3"/>',
        'workflow'      => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><path d="M6.5 10v3a3 3 0 0 0 3 3H14"/><path d="M17.5 14v-3a3 3 0 0 0-3-3H10"/>',
        'code'          => '<path d="m16 18 6-6-6-6"/><path d="m8 6-6 6 6 6"/><path d="m14 4-4 16"/>',
        'layout'        => '<rect x="3" y="3" width="18" height="18" rx="2.5"/><path d="M3 9h18"/><path d="M9 21V9"/>',
        'brain'         => '<path d="M9.5 3A3.5 3.5 0 0 0 6 6.5v.2A3.5 3.5 0 0 0 4 13a3.5 3.5 0 0 0 2 6.3A2.7 2.7 0 0 0 9.5 21 2.5 2.5 0 0 0 12 18.5V5.5A2.5 2.5 0 0 0 9.5 3Z"/><path d="M14.5 3A3.5 3.5 0 0 1 18 6.5v.2A3.5 3.5 0 0 1 20 13a3.5 3.5 0 0 1-2 6.3 2.7 2.7 0 0 1-3.5 1.7A2.5 2.5 0 0 1 12 18.5"/>',
        'network'       => '<circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M12 7.5v4"/><path d="m12 11.5-5.5 5.5"/><path d="m12 11.5 5.5 5.5"/>',
        'wrench'        => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-8 8-2-2 1.3-1.3-6.6-6.6L4.4 12.4 2.3 10.3a4 4 0 0 1 5-5l1.4 1.4 1.4-1.4 1.4 1.4 1.4-1.4Z"/>',
        'database'      => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'user-check'    => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="m16 11 2 2 4-4"/>',
        'users'         => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="M16 4.1a4 4 0 0 1 0 7.8"/><path d="M22 21a7 7 0 0 0-4-6.3"/>',
        'file-text'     => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'mail'          => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m3 7 9 6 9-6"/>',
        'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'calendar'      => '<rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/>',
        'plug'          => '<path d="M9 2v6"/><path d="M15 2v6"/><path d="M6 8h12v3a6 6 0 0 1-12 0Z"/><path d="M12 17v5"/>',
        'git-branch'    => '<circle cx="6" cy="6" r="2.5"/><circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="8" r="2.5"/><path d="M6 8.5v7"/><path d="M18 10.5a6 6 0 0 1-6 6H8.5"/>',
        'shield-check'  => '<path d="M12 3 4 6v6c0 5 3.4 8.4 8 9 4.6-.6 8-4 8-9V6Z"/><path d="m9 12 2 2 4-4"/>',
        'search'        => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'zap'           => '<path d="M13 2 4 14h7l-1 8 9-12h-7Z"/>',
        'sparkles'      => '<path d="M12 3l1.8 4.9L19 9.7l-5.2 1.8L12 16.5l-1.8-5L5 9.7l5.2-1.8Z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8Z"/>',
        'layers'        => '<path d="m12 3 9 5-9 5-9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'cpu'           => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5" rx=".5"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
        'chart'         => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
        'bar-chart'     => '<path d="M3 21h18"/><rect x="5" y="11" width="3" height="7" rx=".5"/><rect x="10.5" y="6" width="3" height="12" rx=".5"/><rect x="16" y="13" width="3" height="5" rx=".5"/>',
        'message'       => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 21l1.9-5.4A8 8 0 1 1 21 12Z"/>',
        'gauge'         => '<path d="M12 14l4-4"/><path d="M3.3 17a10 10 0 1 1 17.4 0"/>',
        'rocket'        => '<path d="M5 15c-1.5 1.3-2 5-2 5s3.7-.5 5-2"/><path d="M9 15 6 12c1-3 3.5-7 9-9 1.5-.5 3-.7 5-.9-.2 2-.4 3.5-.9 5-2 5.5-6 8-9 9Z"/><circle cx="15" cy="9" r="1.5"/>',
        'target'        => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'compass'       => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5Z"/>',
        'settings'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
        'eye'           => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'trash'         => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'edit'          => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
        'image'         => '<rect x="3" y="3" width="18" height="18" rx="2.5"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
        'logout'        => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'home'          => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/>',
        'star'          => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1Z"/>',
        'quote'         => '<path d="M3 21c3 0 7-1 7-8V5H3v8h4c0 4-2 5-4 5Z"/><path d="M14 21c3 0 7-1 7-8V5h-7v8h4c0 4-2 5-4 5Z"/>',
        'globe'         => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18Z"/>',
        'phone'         => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'map-pin'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'send'          => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
        'refresh'       => '<path d="M21 12a9 9 0 0 1-15.5 6.3L3 16"/><path d="M3 12a9 9 0 0 1 15.5-6.3L21 8"/><path d="M21 3v5h-5"/><path d="M3 21v-5h5"/>',
        'alert'         => '<circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
        'inbox'         => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5h13l3.5 7v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6Z"/>',
        'grip'          => '<circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/>',
        'upload'        => '<path d="M12 15V3"/><path d="m7 8 5-5 5 5"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>',
        'lock'          => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'linkedin'      => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6Z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
        'x-social'      => '<path d="M4 4h4.3L20 20h-4.3Z"/><path d="M19.6 4 13.5 11"/><path d="M10.5 13 4.4 20"/>',
        'github'        => '<path d="M9 19c-4.3 1.4-4.3-2.5-6-3m12 5v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 0 0-1.3-3.2 4.2 4.2 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12.3 12.3 0 0 0-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 0 0-.1 3.2A4.6 4.6 0 0 0 4 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>',
        'instagram'     => '<rect x="2.5" y="2.5" width="19" height="19" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8"/>',
        'youtube'       => '<path d="M2.5 17a24 24 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.6 49.6 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24 24 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.6 49.6 0 0 1-16.2 0A2 2 0 0 1 2.5 17Z"/><path d="m10 15 5-3-5-3Z"/>',
        'facebook'      => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3Z"/>',
        'dribbble'      => '<circle cx="12" cy="12" r="9"/><path d="M19.1 5.2C15.5 9.5 10 11 3.3 10.4"/><path d="M21 13.1c-6.6-1.4-12.2 1-15.8 6.4"/><path d="M8.6 3.7C12.9 9.5 15.5 14.6 16 20.5"/>',
    ];
}

/** Render an icon. $label makes it accessible; otherwise it is decorative. */
function icon(string $name, string $class = '', ?string $label = null, float $stroke = 1.6): string
{
    $paths = icon_paths()[$name] ?? icon_paths()['sparkles'];
    $a11y  = $label ? 'role="img" aria-label="' . e($label) . '"' : 'aria-hidden="true" focusable="false"';
    return '<svg class="icon ' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . $stroke . '" stroke-linecap="round" stroke-linejoin="round" ' . $a11y . '>' . $paths . '</svg>';
}

/** Icon names offered in admin selects. */
function icon_names(): array
{
    return array_keys(icon_paths());
}
