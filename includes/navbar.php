<?php
$nav = [
    ['Home', '/'],
    ['AI Solutions', '/ai-solutions/'],
    ['Case Studies', '/case-studies/'],
    
];
$cur = current_path();
$isActive = static fn(string $href) => $href === '/' ? $cur === '/' : str_starts_with($cur, $href);
?>
<header class="site-header" data-header>
    <div class="container site-header__inner">
        <?php component('logo', ['class' => 'logo--header']); ?>

        <nav class="nav" aria-label="Primary">
            <ul class="nav__list">
                <?php foreach ($nav as [$label, $href]): $a = $isActive($href); ?>
                    <li><a class="nav__link<?= $a ? ' is-active' : '' ?>" href="<?= path($href) ?>"<?= $a ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <?php component('button', ['label' => 'Get Started', 'href' => '/contact/', 'size' => 'sm', 'class' => 'site-header__cta' . (str_starts_with($cur, '/contact/') ? ' is-active' : ''), 'magnetic' => true]); ?>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="Open menu" data-nav-toggle>
                <span class="nav-toggle__bar"></span><span class="nav-toggle__bar"></span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobile-nav" data-mobile-nav aria-hidden="true">
    <div class="mobile-nav__glow" aria-hidden="true"></div>
    <nav class="mobile-nav__inner container" aria-label="Mobile">
        <ol class="mobile-nav__list">
            <?php foreach (array_merge($nav, [['Get Started', '/contact/']]) as $i => [$label, $href]): $a = $isActive($href); ?>
                <li style="--i:<?= $i ?>">
                    <a class="mobile-nav__link<?= $a ? ' is-active' : '' ?>" href="<?= path($href) ?>"<?= $a ? ' aria-current="page"' : '' ?> tabindex="-1">
                        <span class="mobile-nav__num">0<?= $i + 1 ?></span><?= e($label) ?>
                        <?= icon('arrow-up-right', 'mobile-nav__arrow') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
        <div class="mobile-nav__meta">
            <a href="mailto:<?= e(setting('contact_email')) ?>" tabindex="-1"><?= icon('mail') ?><?= e(setting('contact_email')) ?></a>
            <a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('contact_phone'))) ?>" tabindex="-1"><?= icon('phone') ?><?= e(setting('contact_phone')) ?></a>
        </div>
    </nav>
</div>
