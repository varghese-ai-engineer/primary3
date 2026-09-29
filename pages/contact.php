<?php
/** Contact / Get Started */
$page['title']       = "Get Started — Let's Build Something Intelligent";
$page['description'] = setting('contact_hero_description');
$page['breadcrumbs'] = [['Home', '/'], ['Get Started', '/contact/']];
$page['schema']      = [['@type' => 'ContactPage', 'name' => 'Contact ' . setting('site_name'), 'url' => url('contact/')]];

$faqs = faqs('contact');
if ($faqs) {
    $page['schema'][] = [
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']]], $faqs),
    ];
}
$budgets   = setting_list('budget_options');
$phoneHref = preg_replace('/[^\d+]/', '', setting('contact_phone'));
$trap      = sign_value((string)time());
$sentOk    = flash('contact_ok');     // non-JS fallback messages
$sentErr   = flash('contact_err');

component('page-hero', [
    'eyebrow' => setting('contact_hero_eyebrow'), 'title' => setting('contact_hero_title'),
    'description' => setting('contact_hero_description'), 'breadcrumbs' => $page['breadcrumbs'],
]);
?>
<section class="section section--contact" aria-label="Contact">
    <div class="container contact">
        <aside class="contact__info" data-reveal>
            
            
            <div class="info-card">
                <span class="info-card__icon"><?= icon('clock') ?></span>
                <div><p class="info-card__k">Availability</p><p class="info-card__v"><?= e(setting('availability')) ?></p><p class="info-card__s"><span class="dot-live" aria-hidden="true"></span><?= e(setting('response_time')) ?></p></div>
            </div>
            <div class="contact__steps">
                <p class="contact__steps-title">What happens next</p>
                <ol>
                    <li><span>1</span>We review your brief within one business day.</li>
                    <li><span>2</span>A 30-minute discovery call with an engineer.</li>
                    <li><span>3</span>A fixed-scope proposal with an ROI estimate.</li>
                </ol>
            </div>
        </aside>

        <div class="form-card" data-reveal style="--delay:.1s">
            <div class="form-card__glow" aria-hidden="true"></div>
            <form class="form"<?= $sentOk ? ' hidden' : '' ?> action="<?= path('/api/contact') ?>" method="post" novalidate data-contact-form>
                <?= csrf_field() ?>
                <input type="hidden" name="_ts" value="<?= e($trap) ?>">
                <div class="hp" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <h2 class="form__title">Tell us about your project</h2>
                <p class="form__note">Fields marked <span aria-hidden="true">*</span><span class="sr-only">with an asterisk</span> are required.</p>

                <div class="form__status<?= $sentErr ? ' is-error' : '' ?>" role="alert" aria-live="assertive" data-form-status<?= $sentErr ? '' : ' hidden' ?>><?= $sentErr ? icon('alert') . e($sentErr) : '' ?></div>

                <div class="form__grid">
                    <div class="field">
                        <label class="field__label" for="f-name">Full Name <span aria-hidden="true">*</span></label>
                        <input class="field__input" id="f-name" name="name" type="text" autocomplete="name" required maxlength="120" aria-describedby="e-name">
                        <p class="field__error" id="e-name" data-error-for="name"></p>
                    </div>
                    <div class="field">
                        <label class="field__label" for="f-email">Email Address <span aria-hidden="true">*</span></label>
                        <input class="field__input" id="f-email" name="email" type="email" autocomplete="email" required maxlength="190" aria-describedby="e-email">
                        <p class="field__error" id="e-email" data-error-for="email"></p>
                    </div>
                    <div class="field">
                        <label class="field__label" for="f-phone">Phone Number <span class="field__opt">(optional)</span></label>
                        <input class="field__input" id="f-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" aria-describedby="e-phone">
                        <p class="field__error" id="e-phone" data-error-for="phone"></p>
                    </div>
                    <div class="field">
                        <label class="field__label" for="f-company">Company Name <span class="field__opt">(optional)</span></label>
                        <input class="field__input" id="f-company" name="company" type="text" autocomplete="organization" maxlength="150">
                        <p class="field__error" data-error-for="company"></p>
                    </div>
                    <div class="field field--full">
                        <label class="field__label" for="f-budget">Budget Range</label>
                        <div class="select-wrap">
                            <select class="field__input" id="f-budget" name="budget">
                                <option value="">Select a range</option>
                                <?php foreach ($budgets as $b): ?><option value="<?= e($b) ?>"><?= e($b) ?></option><?php endforeach; ?>
                            </select>
                            <?= icon('chevron-down', 'select-wrap__icon') ?>
                        </div>
                        <p class="field__error" data-error-for="budget"></p>
                    </div>
                    <div class="field field--full">
                        <label class="field__label" for="f-message">Project Details <span aria-hidden="true">*</span></label>
                        <textarea class="field__input" id="f-message" name="message" rows="6" required minlength="20" maxlength="5000" aria-describedby="e-message" placeholder="What would you like to automate or build? Timelines, tools, goals…"></textarea>
                        <p class="field__error" id="e-message" data-error-for="message"></p>
                    </div>
                </div>

                <div class="form__foot">
                    <p class="form__privacy"><?= icon('lock') ?>Your details are only used to reply. <a class="link-u" href="<?= path('/privacy-policy/') ?>">Privacy Policy</a></p>
                    <?php component('button', ['label' => 'Send Message', 'type' => 'submit', 'icon' => 'send', 'size' => 'lg', 'class' => 'glow form__submit', 'magnetic' => true]); ?>
                </div>
            </form>

            <div class="form-success" data-form-success<?= $sentOk ? '' : ' hidden' ?> tabindex="-1">
                <span class="form-success__icon" aria-hidden="true"><?= icon('check', '', null, 2) ?></span>
                <h2 class="form-success__title"><?= e(setting('contact_success_title')) ?></h2>
                <p class="form-success__text"><?= e(setting('contact_success_text')) ?></p>
                <?php component('button', ['label' => 'Explore our case studies', 'href' => '/case-studies/', 'variant' => 'ghost', 'size' => 'sm']); ?>
            </div>
        </div>
    </div>
</section>

<?php if ($faqs): ?>
<section class="section section--alt" aria-labelledby="faq-title">
    <div class="container faq-layout">
        <?php component('section-heading', ['eyebrow' => 'FAQ', 'title' => 'Questions, *answered*', 'id' => 'faq-title', 'description' => "Can't find what you're looking for? Email us — a real engineer will reply."]); ?>
        <?php component('faq', ['items' => $faqs]); ?>
    </div>
</section>
<?php endif; ?>
