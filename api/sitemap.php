<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/sitemap-error.log');

header("Content-type: text/xml");
include_once(__DIR__ . '/../../connect.php');

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$pages = [
    'https://khodiyarlab.com/',
    'https://khodiyarlab.com/products',
];

foreach ($pages as $page) {
    $xml .= '<url>';
    $xml .= '<loc>' . htmlspecialchars($page) . '</loc>';
    $xml .= '<priority>1.0</priority>';
    $xml .= '<changefreq>daily</changefreq>';
    $xml .= '</url>';
}

// Fetch product pages
$sql = "SELECT id FROM products ORDER BY id ASC";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $productUrl = 'https://khodiyarlab.com/product/' . $row['id'];
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($productUrl) . '</loc>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '</url>';
    }
} else {
    error_log("DB Error: " . $conn->error);
}

$xml .= '</urlset>';
echo $xml;
?>
