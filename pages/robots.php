<?php
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=86400');
echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /api/\n";
echo "Disallow: /install.php\n\n";
echo 'Sitemap: ' . url('sitemap.xml') . "\n";
