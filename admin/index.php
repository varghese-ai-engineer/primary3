<?php
/** Admin dashboard */
declare(strict_types=1);
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

$stats = [
    ['Total projects', (int)DB::value('SELECT COUNT(*) FROM case_studies'), 'layers', 'case-studies.php'],
    ['Active case studies', (int)DB::value('SELECT COUNT(*) FROM case_studies WHERE is_published = 1'), 'eye', 'case-studies.php'],
    ['Total enquiries', (int)DB::value('SELECT COUNT(*) FROM contact_submissions'), 'inbox', 'messages.php'],
    ['Team members', (int)DB::value('SELECT COUNT(*) FROM team_members WHERE is_active = 1'), 'users', 'resource.php?r=team'],
];
$unread = unread_count();

// Enquiries per day, last 14 days (DATE() works on MySQL and SQLite)
$since = date('Y-m-d', strtotime('-13 days'));
$byDay = [];
foreach (DB::all('SELECT DATE(created_at) AS d, COUNT(*) AS n FROM contact_submissions WHERE created_at >= ? GROUP BY DATE(created_at)', [$since . ' 00:00:00']) as $r) {
    $byDay[$r['d']] = (int)$r['n'];
}
$days = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $days[$d] = $byDay[$d] ?? 0;
}
$maxDay = max(1, max($days));
$period = array_sum($days);

$byCat  = DB::all('SELECT COALESCE(c.name, \'Uncategorised\') AS name, COUNT(cs.id) AS n FROM case_studies cs LEFT JOIN case_study_categories c ON c.id = cs.category_id GROUP BY c.name ORDER BY n DESC');
$maxCat = max(1, ...array_map(fn($r) => (int)$r['n'], $byCat ?: [['n' => 1]]));
$byStatus = [];
foreach (DB::all('SELECT status, COUNT(*) AS n FROM contact_submissions GROUP BY status') as $r) $byStatus[$r['status']] = (int)$r['n'];

$recent = DB::all('SELECT * FROM contact_submissions ORDER BY created_at DESC, id DESC LIMIT 6');

$title  = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/_inc/layout-top.php';
?>
<p class="a-hello">Good <?= (int)date('G') < 12 ? 'morning' : ((int)date('G') < 18 ? 'afternoon' : 'evening') ?>, <?= e(explode(' ', $me['name'])[0]) ?>.<?php if ($unread): ?> You have <a href="<?= admin_url('messages.php?status=new') ?>"><?= $unread ?> new enquir<?= $unread === 1 ? 'y' : 'ies' ?></a>.<?php endif; ?></p>

<div class="a-stats">
    <?php foreach ($stats as [$label, $n, $ic, $href]): ?>
        <a class="a-stat" href="<?= admin_url($href) ?>">
            <span class="a-stat__icon"><?= icon($ic) ?></span>
            <span class="a-stat__n"><?= $n ?></span>
            <span class="a-stat__l"><?= e($label) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="a-dash">
    <section class="a-card">
        <div class="a-card__head"><h2 class="a-card__title">Enquiries — last 14 days</h2><span class="a-muted"><?= $period ?> total</span></div>
        <figure class="a-chart" role="img" aria-label="Bar chart of enquiries per day over the last 14 days, <?= $period ?> in total">
            <svg viewBox="0 0 560 200" preserveAspectRatio="none" aria-hidden="true">
                <?php foreach ([0, .5, 1] as $g): $y = 170 - $g * 150; ?>
                    <line x1="0" x2="560" y1="<?= $y ?>" y2="<?= $y ?>" class="a-chart__grid"/>
                    <text x="0" y="<?= $y - 4 ?>" class="a-chart__axis"><?= round($maxDay * $g) ?></text>
                <?php endforeach; ?>
                <?php $i = 0; foreach ($days as $d => $n): $h = $n / $maxDay * 150; $x = 24 + $i * 38; ?>
                    <rect x="<?= $x ?>" y="<?= 170 - $h ?>" width="24" height="<?= max($h, 1) ?>" rx="4" class="a-chart__bar<?= $d === date('Y-m-d') ? ' is-today' : '' ?>"><title><?= date('M j', strtotime($d)) ?>: <?= $n ?></title></rect>
                    <?php if ($i % 2 === 0): ?><text x="<?= $x + 12 ?>" y="192" text-anchor="middle" class="a-chart__axis"><?= date('j M', strtotime($d)) ?></text><?php endif; ?>
                <?php $i++; endforeach; ?>
            </svg>
        </figure>
    </section>

    <section class="a-card">
        <div class="a-card__head"><h2 class="a-card__title">Pipeline</h2></div>
        <ul class="a-meter-list">
            <?php $totalMsg = max(1, array_sum($byStatus)); foreach (['new' => 'Unread', 'read' => 'Read', 'contacted' => 'Contacted'] as $k => $l): $n = $byStatus[$k] ?? 0; ?>
                <li><span><?= $l ?></span><span class="a-meter"><span class="a-meter__fill a-meter__fill--<?= $k ?>" style="width:<?= round($n / $totalMsg * 100) ?>%"></span></span><strong><?= $n ?></strong></li>
            <?php endforeach; ?>
        </ul>
        <h3 class="a-card__sub">Case studies by category</h3>
        <ul class="a-meter-list">
            <?php foreach ($byCat as $c): ?>
                <li><span><?= e($c['name']) ?></span><span class="a-meter"><span class="a-meter__fill" style="width:<?= round($c['n'] / $maxCat * 100) ?>%"></span></span><strong><?= (int)$c['n'] ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

<section class="a-card">
    <div class="a-card__head"><h2 class="a-card__title">Recent enquiries</h2><a class="a-btn a-btn--ghost a-btn--sm" href="<?= admin_url('messages.php') ?>">View all</a></div>
    <?php if (!$recent): ?>
        <div class="a-empty"><?= icon('inbox') ?><p>No enquiries yet. Submissions from the Get Started form will appear here.</p></div>
    <?php else: ?>
        <div class="a-table-wrap">
            <table class="a-table">
                <thead><tr><th scope="col">From</th><th scope="col">Company</th><th scope="col">Budget</th><th scope="col">Status</th><th scope="col">Received</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $r): ?>
                    <tr class="<?= $r['status'] === 'new' ? 'is-unread' : '' ?>">
                        <td data-label="From"><a class="a-strong" href="<?= admin_url('message.php?id=' . (int)$r['id']) ?>"><?= e($r['name']) ?></a><br><small class="a-muted"><?= e($r['email']) ?></small></td>
                        <td data-label="Company"><?= e($r['company'] ?: '—') ?></td>
                        <td data-label="Budget"><?= e($r['budget'] ?: '—') ?></td>
                        <td data-label="Status"><?= status_badge($r['status']) ?></td>
                        <td data-label="Received"><?= e(time_ago($r['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<div class="a-quick">
    <a class="a-quick__item" href="<?= admin_url('case-study.php') ?>"><?= icon('plus') ?>New case study</a>
    <a class="a-quick__item" href="<?= admin_url('settings.php?g=home') ?>"><?= icon('edit') ?>Edit home page</a>
    <a class="a-quick__item" href="<?= admin_url('resource.php?r=metrics') ?>"><?= icon('bar-chart') ?>Update metrics</a>
    <a class="a-quick__item" href="<?= admin_url('media.php') ?>"><?= icon('upload') ?>Upload media</a>
</div>
<?php require __DIR__ . '/_inc/layout-bottom.php';
