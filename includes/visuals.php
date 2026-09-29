<?php
/**
 * Generated abstract technology visuals (inline SVG).
 * A consistent, lightweight image system — no stock photography.
 * Styling comes from CSS classes (.v-*) so the palette stays centralised.
 */
declare(strict_types=1);

function visual_names(): array
{
    return ['network' => 'AI network', 'chat' => 'Conversational agent', 'pipeline' => 'Automation pipeline', 'dashboard' => 'Analytics dashboard'];
}

function visual(string $name, string $label = '', string $class = ''): string
{
    static $n = 0;
    $n++;
    $id  = 'v' . $n;
    $fn  = 'visual_' . (array_key_exists($name, visual_names()) ? $name : 'network');
    $aria = $label !== '' ? 'role="img" aria-label="' . e($label) . '"' : 'aria-hidden="true"';
    $defs = '<defs>'
        . '<linearGradient id="' . $id . 'g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" style="stop-color:var(--accent-bright)"/><stop offset="1" style="stop-color:var(--accent-cyan)"/></linearGradient>'
        . '<radialGradient id="' . $id . 'r" cx="50%" cy="50%" r="50%"><stop offset="0" style="stop-color:var(--accent-primary);stop-opacity:.45"/><stop offset="1" style="stop-color:var(--accent-primary);stop-opacity:0"/></radialGradient>'
        . '<pattern id="' . $id . 'p" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M20 0H0V20" fill="none" class="v-grid"/></pattern>'
        . '</defs>';
    $fit = str_contains($class, 'cover') ? 'meet' : 'slice';   // wide covers show the whole composition
    return '<svg class="visual visual--' . e($name) . ' ' . e($class) . '" viewBox="0 0 400 260" preserveAspectRatio="xMidYMid ' . $fit . '" ' . $aria . '>'
        . $defs
        . '<rect width="400" height="260" fill="url(#' . $id . 'p)"/>'
        . $fn($id)
        . '</svg>';
}

function visual_network(string $id): string
{
    $nodes = [[200, 130, 16], [90, 60, 7], [320, 58, 7], [70, 190, 6], [330, 196, 8], [200, 36, 5], [200, 226, 5], [130, 130, 4], [270, 130, 4]];
    $out = '<circle cx="200" cy="130" r="120" fill="url(#' . $id . 'r)"/>';
    foreach (array_slice($nodes, 1) as $i => [$x, $y]) {
        $out .= '<path class="v-line v-flow" style="--d:' . ($i * .4) . 's" d="M200 130 L' . $x . ' ' . $y . '"/>';
    }
    $out .= '<path class="v-line v-dim" d="M90 60 Q200 10 320 58 M70 190 Q200 250 330 196 M90 60 L70 190 M320 58 L330 196"/>';
    foreach ($nodes as $i => [$x, $y, $r]) {
        $out .= $i === 0
            ? '<circle cx="' . $x . '" cy="' . $y . '" r="34" class="v-ring"/><circle cx="' . $x . '" cy="' . $y . '" r="' . $r . '" fill="url(#' . $id . 'g)" class="v-core"/>'
            : '<circle cx="' . $x . '" cy="' . $y . '" r="' . $r . '" class="v-node"/>';
    }
    return $out;
}

function visual_chat(string $id): string
{
    return '<circle cx="300" cy="120" r="110" fill="url(#' . $id . 'r)"/>'
        . '<rect x="40" y="44" width="190" height="44" rx="14" class="v-panel"/>'
        . '<rect x="56" y="60" width="120" height="6" rx="3" class="v-bar"/><rect x="56" y="72" width="80" height="5" rx="2.5" class="v-bar v-dim"/>'
        . '<rect x="150" y="104" width="210" height="62" rx="14" class="v-panel v-panel--accent"/>'
        . '<rect x="166" y="120" width="150" height="6" rx="3" fill="url(#' . $id . 'g)"/><rect x="166" y="133" width="170" height="5" rx="2.5" class="v-bar"/><rect x="166" y="145" width="110" height="5" rx="2.5" class="v-bar v-dim"/>'
        . '<circle cx="376" cy="118" r="10" fill="url(#' . $id . 'g)"/>'
        . '<rect x="40" y="184" width="150" height="40" rx="14" class="v-panel"/>'
        . '<circle cx="62" cy="204" r="3" class="v-node v-blink"/><circle cx="74" cy="204" r="3" class="v-node v-blink" style="--d:.2s"/><circle cx="86" cy="204" r="3" class="v-node v-blink" style="--d:.4s"/>'
        . '<text x="250" y="210" class="v-label">&lt; 3s</text>';
}

function visual_pipeline(string $id): string
{
    $out = '<circle cx="200" cy="130" r="120" fill="url(#' . $id . 'r)"/>'
        . '<path class="v-line v-flow" d="M84 130 H316"/>';
    $steps = [[30, 'doc'], [140, 'ai'], [250, 'ok']];
    foreach ($steps as $i => [$x, $kind]) {
        $cls = $i === 1 ? 'v-panel v-panel--accent' : 'v-panel';
        $out .= '<rect x="' . ($x + 4) . '" y="92" width="112" height="76" rx="14" class="' . $cls . '"/>';
        if ($kind === 'doc') {
            $out .= '<rect x="62" y="108" width="40" height="46" rx="4" class="v-outline"/><rect x="70" y="120" width="24" height="4" rx="2" class="v-bar"/><rect x="70" y="130" width="18" height="4" rx="2" class="v-bar v-dim"/><rect x="70" y="140" width="22" height="4" rx="2" class="v-bar v-dim"/>';
        } elseif ($kind === 'ai') {
            $out .= '<circle cx="200" cy="130" r="18" fill="url(#' . $id . 'g)" class="v-core"/><circle cx="200" cy="130" r="28" class="v-ring"/>';
        } else {
            $out .= '<circle cx="310" cy="130" r="18" class="v-outline"/><path d="M301 130l6 6 12-12" class="v-check"/>';
        }
    }
    $out .= '<text x="36" y="200" class="v-label v-dim">PDF</text><text x="178" y="200" class="v-label v-dim">EXTRACT</text><text x="286" y="200" class="v-label v-dim">APPROVE</text>';
    return $out;
}

function visual_dashboard(string $id): string
{
    $bars = [60, 92, 74, 120, 98, 140, 128];
    $out  = '<circle cx="260" cy="90" r="120" fill="url(#' . $id . 'r)"/>'
        . '<rect x="28" y="24" width="344" height="212" rx="14" class="v-panel"/>'
        . '<circle cx="46" cy="42" r="3.5" class="v-node v-dim"/><circle cx="58" cy="42" r="3.5" class="v-node v-dim"/><circle cx="70" cy="42" r="3.5" class="v-node v-dim"/>'
        . '<rect x="44" y="60" width="96" height="44" rx="8" class="v-outline"/><rect x="54" y="72" width="40" height="5" rx="2.5" class="v-bar v-dim"/><rect x="54" y="84" width="60" height="9" rx="3" fill="url(#' . $id . 'g)"/>'
        . '<rect x="152" y="60" width="96" height="44" rx="8" class="v-outline"/><rect x="162" y="72" width="40" height="5" rx="2.5" class="v-bar v-dim"/><rect x="162" y="84" width="50" height="9" rx="3" class="v-bar"/>'
        . '<rect x="260" y="60" width="96" height="44" rx="8" class="v-outline"/><rect x="270" y="72" width="40" height="5" rx="2.5" class="v-bar v-dim"/><rect x="270" y="84" width="66" height="9" rx="3" class="v-bar"/>';
    foreach ($bars as $i => $h) {
        $x    = 52 + $i * 24;
        $out .= '<rect x="' . $x . '" y="' . (220 - $h * .7) . '" width="14" height="' . ($h * .7) . '" rx="3" class="' . ($i === 5 ? '' : 'v-bar v-dim') . '"' . ($i === 5 ? ' fill="url(#' . $id . 'g)"' : '') . '/>';
    }
    $out .= '<path class="v-line v-accent-line" d="M226 196 C250 170 262 186 282 150 S320 140 356 118"/>'
        . '<circle cx="356" cy="118" r="4" fill="url(#' . $id . 'g)"/>';
    return $out;
}
