<?php
/**
 * Read-side data access for public pages.
 * Every function is memoised per request to avoid duplicate queries.
 */
declare(strict_types=1);

function memo(string $key, callable $fn)
{
    static $cache = [];
    if (!array_key_exists($key, $cache)) {
        $cache[$key] = $fn();
    }
    return $cache[$key];
}

/** All site settings in one query. */
function settings(): array
{
    return memo('settings', static function () {
        $out = [];
        foreach (DB::all('SELECT setting_key, value FROM site_settings') as $r) {
            $out[$r['setting_key']] = $r['value'];
        }
        return $out;
    });
}

function setting(string $key, string $default = ''): string
{
    $v = settings()[$key] ?? null;
    return ($v === null || $v === '') ? $default : (string)$v;
}

function setting_list(string $key): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', setting($key))), 'strlen'));
}

function services(string $section): array
{
    return memo("services:$section", fn() => DB::all(
        'SELECT * FROM services WHERE section = ? AND is_active = 1 ORDER BY sort_order, id',
        [$section]
    ));
}

function metrics(): array
{
    return memo('metrics', fn() => DB::all('SELECT * FROM metrics WHERE is_active = 1 ORDER BY sort_order, id'));
}

function clients(): array
{
    return memo('clients', fn() => DB::all('SELECT * FROM clients WHERE is_active = 1 ORDER BY sort_order, id'));
}

function technologies(?string $flag = null): array
{
    $where = 'is_active = 1';
    if ($flag === 'hero') {
        $where .= ' AND show_in_hero = 1';
    } elseif ($flag === 'ticker') {
        $where .= ' AND show_in_ticker = 1';
    }
    return memo("tech:$flag", fn() => DB::all("SELECT * FROM technology_stack WHERE $where ORDER BY sort_order, id"));
}

function technologies_by_category(): array
{
    $out = [];
    foreach (technologies() as $t) {
        $out[$t['category']][] = $t;
    }
    return $out;
}

function categories(): array
{
    return memo('categories', fn() => DB::all(
        'SELECT c.*, (SELECT COUNT(*) FROM case_studies cs WHERE cs.category_id = c.id AND cs.is_published = 1) AS total
         FROM case_study_categories c ORDER BY c.sort_order, c.id'
    ));
}

/** Published case studies (optionally filtered by category slug), metrics attached. */
function case_studies(?string $categorySlug = null, bool $featuredOnly = false, int $limit = 0): array
{
    $sql = 'SELECT cs.*, c.name AS category_name, c.slug AS category_slug
            FROM case_studies cs LEFT JOIN case_study_categories c ON c.id = cs.category_id
            WHERE cs.is_published = 1';
    $params = [];
    if ($categorySlug) {
        $sql .= ' AND c.slug = ?';
        $params[] = $categorySlug;
    }
    if ($featuredOnly) {
        $sql .= ' AND cs.is_featured = 1';
    }
    $sql .= ' ORDER BY cs.sort_order, cs.id';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int)$limit;
    }
    $rows = DB::all($sql, $params);
    return attach_metrics($rows);
}

function attach_metrics(array $rows): array
{
    if (!$rows) {
        return $rows;
    }
    $ids = array_column($rows, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $map = [];
    foreach (DB::all("SELECT * FROM case_study_metrics WHERE case_study_id IN ($in) ORDER BY sort_order, id", $ids) as $m) {
        $map[$m['case_study_id']][] = $m;
    }
    foreach ($rows as &$r) {
        $r['metrics'] = $map[$r['id']] ?? [];
    }
    return $rows;
}

function case_study(string $slug): ?array
{
    $row = DB::one(
        'SELECT cs.*, c.name AS category_name, c.slug AS category_slug
         FROM case_studies cs LEFT JOIN case_study_categories c ON c.id = cs.category_id
         WHERE cs.slug = ? AND cs.is_published = 1',
        [$slug]
    );
    if (!$row) {
        return null;
    }
    $row = attach_metrics([$row])[0];
    $row['images']      = DB::all('SELECT * FROM case_study_images WHERE case_study_id = ? ORDER BY sort_order, id', [$row['id']]);
    $row['testimonial'] = DB::one('SELECT * FROM testimonials WHERE case_study_id = ? AND is_active = 1 ORDER BY sort_order, id LIMIT 1', [$row['id']]);
    return $row;
}

/** Adjacent case study for "next project" navigation. */
function next_case_study(array $current): ?array
{
    $all = case_studies();
    if (count($all) < 2) {
        return null;
    }
    foreach ($all as $i => $cs) {
        if ((int)$cs['id'] === (int)$current['id']) {
            return $all[($i + 1) % count($all)];
        }
    }
    return $all[0];
}

function team(): array
{
    return memo('team', fn() => DB::all('SELECT * FROM team_members WHERE is_active = 1 ORDER BY sort_order, id'));
}

function timeline(): array
{
    return memo('timeline', fn() => DB::all('SELECT * FROM timeline WHERE is_active = 1 ORDER BY sort_order, year, id'));
}

function faqs(string $page): array
{
    return memo("faqs:$page", fn() => DB::all('SELECT * FROM faqs WHERE page = ? AND is_active = 1 ORDER BY sort_order, id', [$page]));
}

function social_links(): array
{
    return memo('social', fn() => DB::all('SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order, id'));
}

function seo_meta(string $pageKey): ?array
{
    return memo("seo:$pageKey", fn() => DB::one('SELECT * FROM seo_meta WHERE page_key = ?', [$pageKey]));
}
