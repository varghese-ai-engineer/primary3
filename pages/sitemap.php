<?php
/** Dynamic XML sitemap (static pages + published case studies). */
$today = date('Y-m-d');
$urls  = [
    ['/', '1.0', 'weekly', $today],
    ['/ai-solutions/', '0.9', 'monthly', $today],
    ['/case-studies/', '0.9', 'weekly', $today],
    ['/about/', '0.7', 'monthly', $today],
    ['/contact/', '0.8', 'yearly', $today],
    ['/privacy-policy/', '0.2', 'yearly', $today],
    ['/terms/', '0.2', 'yearly', $today],
];
foreach (case_studies() as $cs) {
    $urls[] = ['/case-studies/' . $cs['slug'] . '/', '0.8', 'monthly', date('Y-m-d', strtotime($cs['updated_at'] ?: ($cs['published_at'] ?: $cs['created_at'])))];
}
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$loc, $prio, $freq, $mod]) {
    echo "  <url><loc>" . e(url($loc)) . "</loc><lastmod>$mod</lastmod><changefreq>$freq</changefreq><priority>$prio</priority></url>\n";
}
echo '</urlset>';
