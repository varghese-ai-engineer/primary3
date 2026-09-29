<?php /** Empty / no-results state. Props: title, text, action [label, href] */ ?>
<div class="empty-state" role="status">
    <span class="empty-state__icon" aria-hidden="true"><?= icon($icon ?? 'inbox') ?></span>
    <h2 class="empty-state__title"><?= e($title) ?></h2>
    <p class="empty-state__text"><?= e($text) ?></p>
    <?php component('button', ['label' => $action[0] ?? 'View all projects', 'href' => $action[1] ?? '/case-studies/', 'variant' => 'ghost', 'size' => 'sm', 'attrs' => 'data-filter-reset']); ?>
</div>
