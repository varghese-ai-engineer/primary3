<?php
/**
 * Technical SEO: meta tags, Open Graph, Twitter cards and JSON-LD.
 */
declare(strict_types=1);

/**
 * Merge page defaults with admin-managed seo_meta overrides.
 * $page keys: key, title, description, canonical, og_image, type, breadcrumbs[], schema[]
 */
function seo_resolve(array $page): array
{
    $site = setting('site_name', 'Primary Infotech');
    $db   = !empty($page['key']) ? seo_meta($page['key']) : null;

    $title = $db['title'] ?? '' ?: ($page['title'] ?? $site);
    $desc  = $db['description'] ?? '' ?: ($page['description'] ?? setting('company_description'));
    $og    = $db['og_image'] ?? '' ?: ($page['og_image'] ?? '') ?: setting('default_og_image', 'assets/img/og-default.png');

    return array_merge($page, [
        'title'       => $title,
        'full_title'  => str_contains($title, $site) ? $title : "$title | $site",
        'description' => excerpt($desc, 300),
        'canonical'   => $page['canonical'] ?? url(current_path()),
        'og_image'    => url($og),
        'noindex'     => !empty($db['noindex']) || !empty($page['noindex']),
        'type'        => $page['type'] ?? 'website',
    ]);
}

function seo_head(array $m): string
{
    $site = setting('site_name', 'Primary Infotech');
    $h   = [];
    $h[] = '<title>' . e($m['full_title']) . '</title>';
    $h[] = '<meta name="description" content="' . e($m['description']) . '">';
    $h[] = '<link rel="canonical" href="' . e($m['canonical']) . '">';
    $h[] = '<meta name="robots" content="' . ($m['noindex'] ? 'noindex, nofollow' : 'index, follow, max-image-preview:large') . '">';
    // Open Graph
    $h[] = '<meta property="og:site_name" content="' . e($site) . '">';
    $h[] = '<meta property="og:type" content="' . e($m['type']) . '">';
    $h[] = '<meta property="og:title" content="' . e($m['title']) . '">';
    $h[] = '<meta property="og:description" content="' . e($m['description']) . '">';
    $h[] = '<meta property="og:url" content="' . e($m['canonical']) . '">';
    $h[] = '<meta property="og:image" content="' . e($m['og_image']) . '">';
    $h[] = '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">';
    $h[] = '<meta property="og:locale" content="en_US">';
    // Twitter / X
    $h[] = '<meta name="twitter:card" content="summary_large_image">';
    if ($tw = setting('twitter_handle')) {
        $h[] = '<meta name="twitter:site" content="' . e($tw) . '">';
    }
    $h[] = '<meta name="twitter:title" content="' . e($m['title']) . '">';
    $h[] = '<meta name="twitter:description" content="' . e($m['description']) . '">';
    $h[] = '<meta name="twitter:image" content="' . e($m['og_image']) . '">';

    $graph = [schema_organization(), schema_website()];
    if (!empty($m['breadcrumbs'])) {
        $graph[] = schema_breadcrumbs($m['breadcrumbs']);
    }
    foreach ($m['schema'] ?? [] as $s) {
        $graph[] = $s;
    }
    $h[] = '<script type="application/ld+json">' . json_encode(
        ['@context' => 'https://schema.org', '@graph' => $graph],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
    ) . '</script>';

    return implode("\n    ", $h);
}

function schema_organization(): array
{
    $same = array_values(array_map(fn($s) => $s['url'], social_links()));
    return [
        '@type'       => 'ProfessionalService',
        '@id'         => url('/#organization'),
        'name'        => setting('site_name', 'Primary Infotech'),
        'url'         => url('/'),
        'logo'        => url('assets/img/logo-mark.png'),
        'image'       => url(setting('default_og_image', 'assets/img/og-default.png')),
        'description' => setting('company_description'),
        'email'       => setting('contact_email'),
        'telephone'   => setting('contact_phone'),
        'foundingDate'=> setting('founding_year', '2014'),
        'address'     => [
            '@type'           => 'PostalAddress',
            'addressLocality' => setting('address_locality', 'Coimbatore'),
            'addressRegion'   => setting('address_region', 'Tamil Nadu'),
            'addressCountry'  => setting('address_country', 'IN'),
        ],
        'areaServed'  => 'Worldwide',
        'priceRange'  => '$$$',
        'sameAs'      => $same,
    ];
}

function schema_website(): array
{
    return [
        '@type'     => 'WebSite',
        '@id'       => url('/#website'),
        'url'       => url('/'),
        'name'      => setting('site_name', 'Primary Infotech'),
        'publisher' => ['@id' => url('/#organization')],
        'inLanguage'=> 'en',
    ];
}

/** @param array<int,array{0:string,1:string}> $items [name, path] */
function schema_breadcrumbs(array $items): array
{
    $list = [];
    foreach (array_values($items) as $i => [$name, $p]) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => url($p)];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

function schema_services(array $services): array
{
    $items = [];
    foreach ($services as $i => $s) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => [
                '@type'       => 'Service',
                'name'        => $s['title'],
                'description' => $s['excerpt'],
                'provider'    => ['@id' => url('/#organization')],
                'areaServed'  => 'Worldwide',
            ],
        ];
    }
    return ['@type' => 'ItemList', 'name' => 'AI & Automation Services', 'itemListElement' => $items];
}

function schema_case_study(array $cs): array
{
    return [
        '@type'         => 'Article',
        'headline'      => $cs['title'],
        'description'   => excerpt($cs['seo_description'] ?: $cs['excerpt'], 300),
        'articleSection'=> 'Case Study',
        'about'         => $cs['industry'],
        'image'         => url($cs['og_image'] ?: ($cs['hero_image'] ?: setting('default_og_image', 'assets/img/og-default.png'))),
        'datePublished' => date('c', strtotime($cs['published_at'] ?: $cs['created_at'])),
        'dateModified'  => date('c', strtotime($cs['updated_at'] ?: ($cs['published_at'] ?: $cs['created_at']))),
        'author'        => ['@id' => url('/#organization')],
        'publisher'     => ['@id' => url('/#organization')],
        'mainEntityOfPage' => url('case-studies/' . $cs['slug'] . '/'),
    ];
}
