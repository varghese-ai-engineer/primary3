<?php
/** Team member card. Props: member (row), index */
$initials = implode('', array_map(fn($p) => mb_substr($p, 0, 1), array_slice(preg_split('/\s+/', trim($member['name'])), 0, 2)));
$exp      = json_list($member['expertise']);
?>
<article class="team-card" data-reveal style="--delay:<?= 0.12 * ($index ?? 0) ?>s">
    <div class="team-card__photo">
        <?php if (!empty($member['photo'])): ?>
            <?= picture($member['photo'], 'Portrait of ' . $member['name'], 'loading="lazy" decoding="async" width="480" height="560"') ?>
        <?php else: ?>
            <div class="team-card__avatar" role="img" aria-label="<?= e($member['name']) ?>">
                <span><?= e($initials) ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="team-card__body">
        <p class="team-card__role"><?= e($member['role']) ?></p>
        <h3 class="team-card__name"><?= e($member['name']) ?></h3>
        <p class="team-card__bio"><?= e($member['bio']) ?></p>
        <?php if ($exp): ?>
            <ul class="tag-list" aria-label="Expertise">
                <?php foreach ($exp as $x): ?><li class="tag"><?= e($x) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (!empty($member['linkedin'])): ?>
            <a class="btn btn--ghost btn--sm team-card__li" href="<?= e($member['linkedin']) ?>" target="_blank" rel="noopener noreferrer">
                <span class="btn__icon btn__icon--lead"><?= icon('linkedin') ?></span><span class="btn__label">LinkedIn<span class="sr-only"> profile of <?= e($member['name']) ?> (opens in new tab)</span></span>
            </a>
        <?php endif; ?>
    </div>
</article>
