<?php
/** Privacy Policy & Terms (content managed in Settings → Legal) */
$isPrivacy = current_path() === '/privacy-policy/';
$title     = $isPrivacy ? 'Privacy Policy' : 'Terms of Service';
$page['key']         = $isPrivacy ? 'privacy-policy' : 'terms';
$page['title']       = $title;
$page['description'] = $title . ' — ' . setting('site_name');
$page['breadcrumbs'] = [['Home', '/'], [$title, $isPrivacy ? '/privacy-policy/' : '/terms/']];

component('page-hero', ['eyebrow' => 'Legal', 'title' => $title, 'breadcrumbs' => $page['breadcrumbs'], 'class' => 'page-hero--compact']);
?>
<section class="section section--legal">
    <div class="container">
        <div class="prose prose--legal" data-reveal><?= paragraphs(setting($isPrivacy ? 'privacy_content' : 'terms_content')) ?></div>
    </div>
</section>
