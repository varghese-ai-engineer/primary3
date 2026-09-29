<?php
/**
 * Home hero visual — a central intelligence node orchestrating agents & systems.
 * Pure SVG + CSS (transform/opacity/stroke-dashoffset only); pauses when off-screen
 * and is static under prefers-reduced-motion.
 */
$nodes = [
    ['Research Agent', 'search'],
    ['Action Agent', 'zap'],
    ['Validator Agent', 'shield-check'],
    ['Database', 'database'],
    ['Human Approval', 'user-check'],
    ['API', 'plug'],
    ['Workflow', 'workflow'],
];
$n = count($nodes);
$pts = [];
foreach ($nodes as $i => $node) {
    $a = deg2rad(-118 + $i * (360 / $n));
    $pts[] = [50 + 40 * cos($a), 50 + 34 * sin($a)];   // elliptical orbit leaves room for the floating panels
}
?>
<div class="hero-visual" data-hero-visual aria-hidden="true">
    <div class="hero-visual__glow"></div>
    <div class="hero-visual__stage" data-parallax="-0.06">
        <svg class="hero-net" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <linearGradient id="hn-line" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#A78BFA" stop-opacity=".9"/>
                    <stop offset="1" stop-color="#22D3EE" stop-opacity=".9"/>
                </linearGradient>
                <radialGradient id="hn-core" cx="50%" cy="50%" r="50%">
                    <stop offset="0" stop-color="#C4B5FD"/>
                    <stop offset=".45" stop-color="#8B5CF6"/>
                    <stop offset="1" stop-color="#4338CA" stop-opacity="0"/>
                </radialGradient>
            </defs>
            <!-- orbit rings -->
            <ellipse class="hn-orbit" cx="50" cy="50" rx="40" ry="34"/>
            <circle class="hn-orbit hn-orbit--inner" cx="50" cy="50" r="22"/>
            <?php foreach ($pts as $i => [$x, $y]):
                $cx = 50 + ($x - 50) * .45 + ($i % 2 ? 6 : -6);
                $cy = 50 + ($y - 50) * .45 + ($i % 2 ? -6 : 6);
                $d  = sprintf('M50 50 Q%.2f %.2f %.2f %.2f', $cx, $cy, $x, $y); ?>
                <path class="hn-wire" d="<?= $d ?>"/>
                <path class="hn-signal<?= $i % 2 ? ' hn-signal--in' : '' ?>" d="<?= $d ?>" pathLength="100" style="--d:<?= round($i * 0.55, 2) ?>s;--t:<?= 2.6 + ($i % 3) * .5 ?>s"/>
            <?php endforeach; ?>
            <!-- ring connections between neighbours -->
            <?php foreach ($pts as $i => [$x, $y]): [$x2, $y2] = $pts[($i + 1) % $n]; ?>
                <line class="hn-link" x1="<?= round($x, 2) ?>" y1="<?= round($y, 2) ?>" x2="<?= round($x2, 2) ?>" y2="<?= round($y2, 2) ?>"/>
            <?php endforeach; ?>
        </svg>

        <div class="hn-core">
            <span class="hn-core__pulse"></span>
            <span class="hn-core__pulse hn-core__pulse--2"></span>
            <span class="hn-core__orb"><?= icon('brain') ?></span>
            <span class="hn-core__label">AI Orchestrator</span>
        </div>

        <?php foreach ($nodes as $i => [$label, $ic]): [$x, $y] = $pts[$i]; ?>
            <div class="hn-node" style="left:<?= round($x, 2) ?>%;top:<?= round($y, 2) ?>%;--f:<?= 6 + ($i % 4) ?>s;--fd:<?= -$i * 0.9 ?>s">
                <span class="hn-node__icon"><?= icon($ic) ?></span>
                <span class="hn-node__label"><?= e($label) ?></span>
            </div>
        <?php endforeach; ?>

        <span class="particle" style="--x:12%;--y:30%;--p:9s"></span>
        <span class="particle" style="--x:84%;--y:66%;--p:11s;--pd:-3s"></span>
        <span class="particle" style="--x:62%;--y:12%;--p:13s;--pd:-6s"></span>
        <span class="particle" style="--x:30%;--y:88%;--p:10s;--pd:-2s"></span>
        <span class="particle" style="--x:92%;--y:28%;--p:12s;--pd:-8s"></span>
    </div>

    <div class="float-panel float-panel--log" style="--f:8s">
        <div class="float-panel__head"><span class="dots"><i></i><i></i><i></i></span><span>agent.run</span></div>
        <ul class="log">
            <li><span class="log__ok">✓</span> research: 14 sources ranked</li>
            <li><span class="log__ok">✓</span> action: CRM updated</li>
            <li><span class="log__run"></span> validator: checking output…</li>
        </ul>
    </div>
    <div class="float-panel float-panel--stat" style="--f:10s;--fd:-3s">
        <span class="float-panel__k">Pipeline status</span>
        <span class="float-panel__v"><span class="dot-live"></span>All agents healthy</span>
        <span class="spark"><i style="--h:40%"></i><i style="--h:65%"></i><i style="--h:52%"></i><i style="--h:80%"></i><i style="--h:72%"></i><i style="--h:95%"></i></span>
    </div>
</div>
