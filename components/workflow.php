<?php
/**
 * Agentic workflow diagram. Wires are drawn by JS from real node positions,
 * so the same markup works horizontally (desktop) and vertically (mobile).
 */
$agents = [
    ['research', 'Research Agent', 'search', 'Gathers & ranks context'],
    ['action', 'Action Agent', 'zap', 'Calls tools & APIs'],
    ['validator', 'Validator Agent', 'shield-check', 'Checks quality & policy'],
];
?>
<figure class="flow" data-flow aria-labelledby="flow-caption">
    <svg class="flow__wires" aria-hidden="true"><defs>
        <linearGradient id="flow-grad" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="1200" y2="0">
            <stop offset="0" stop-color="#8B5CF6"/><stop offset="1" stop-color="#22D3EE"/>
        </linearGradient>
    </defs></svg>

    <div class="flow__col">
        <div class="flow-node" data-node="request" data-reveal>
            <span class="flow-node__icon"><?= icon('message') ?></span>
            <span class="flow-node__title">User Request</span>
            <span class="flow-node__desc">“Reconcile March invoices”</span>
        </div>
    </div>

    <div class="flow__col">
        <div class="flow-node flow-node--core" data-node="orchestrator" data-reveal style="--delay:.1s">
            <span class="flow-node__icon"><?= icon('brain') ?></span>
            <span class="flow-node__title">AI Orchestrator</span>
            <span class="flow-node__desc">Plans &amp; delegates</span>
        </div>
    </div>

    <div class="flow__col flow__col--agents">
        <?php foreach ($agents as $i => [$key, $title, $ic, $desc]): ?>
            <div class="flow-node flow-node--agent" data-node="<?= $key ?>" data-reveal style="--delay:<?= .2 + $i * .08 ?>s">
                <span class="flow-node__icon"><?= icon($ic) ?></span>
                <span class="flow-node__title"><?= e($title) ?></span>
                <span class="flow-node__desc"><?= e($desc) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flow__col">
        <div class="flow-node flow-node--out" data-node="output" data-reveal style="--delay:.45s">
            <span class="flow-node__icon"><?= icon('check') ?></span>
            <span class="flow-node__title">Final Output</span>
            <span class="flow-node__desc">Verified &amp; delivered</span>
        </div>
    </div>

    <figcaption id="flow-caption" class="sr-only">
        Agentic workflow: a user request goes to the AI Orchestrator, which delegates to a Research Agent,
        an Action Agent and a Validator Agent; their combined, validated work becomes the final output.
    </figcaption>
</figure>
