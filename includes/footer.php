<?php
$phoneHref = preg_replace('/[^\d+]/', '', setting('contact_phone'));
?>
<footer class="site-footer">
    <div class="site-footer__atmos" aria-hidden="true"></div>
    <div class="container">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <?php component('logo', ['class' => 'logo--footer']); ?>
                <p class="site-footer__desc"><?= e(setting('company_description')) ?></p>
                
            </div>

            <nav class="site-footer__col" aria-label="Footer navigation">
                <h2 class="site-footer__title">Navigation</h2>
                <ul>
                    <li><a class="link-u" href="<?= path('/') ?>">Home</a></li>
                    <li><a class="link-u" href="<?= path('/ai-solutions/') ?>">AI Solutions</a></li>
                    <li><a class="link-u" href="<?= path('/case-studies/') ?>">Case Studies</a></li>
                   
                    <li><a class="link-u" href="<?= path('/contact/') ?>">Get Started</a></li>
                </ul>
            </nav>

            <div class="site-footer__col">
                <h2 class="site-footer__title">Services</h2>
                <ul>
                    <?php foreach (services('core') as $s): ?>
                        <li><a class="link-u" href="<?= e(path($s['cta_url'] ?: '/ai-solutions/')) ?>"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                    
                </ul>
            </div>

            <div class="site-footer__brand">
                <h2 class="site-footer__title">Social media</h2>
                <ul class="social" aria-label="Social media">
                    <?php foreach (social_links() as $s): ?>
                        <li><a class="social__link" href="<?= e($s['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($s['platform']) ?> (opens in new tab)"><?= icon($s['icon']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="site-footer__wordmark" aria-hidden="true"><?= e(setting('logo_text', 'Primary')) ?></div>

        <div class="site-footer__bottom">
            <p>© <?= date('Y') ?> <?= e(setting('site_name')) ?>. All rights reserved.</p>
            <p class="site-footer__tag"><span class="dot-live" aria-hidden="true"></span><?= e(setting('footer_tagline')) ?></p>
            <ul class="site-footer__legal">
                <li><a class="link-u" href="<?= path('/privacy-policy/') ?>">Privacy Policy</a></li>
                <li><a class="link-u" href="<?= path('/terms/') ?>">Terms</a></li>
               
            </ul>
        </div>
    </div>
</footer>
