<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

session_write_close();
header_remove('Set-Cookie');
header_remove('Pragma');
header_remove('Expires');
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: public, max-age=300');

echo "User-agent: *\n";
echo 'Disallow: ' . base_path('panel') . "\n";
echo 'Disallow: ' . base_path('test') . "\n";
echo 'Disallow: ' . base_path('migrations/') . "\n";
echo 'Allow: ' . base_path('img/') . "\n";
echo "\nSitemap: " . sitio_url('sitemap.xml') . "\n";
