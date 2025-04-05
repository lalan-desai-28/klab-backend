<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/sitemap-error.log');

header("Content-type: text/xml");

include_once(__DIR__ . '/../../connect.php');

try {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Static Pages
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

    // Check DB connection
    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection not established.");
    }

    // Fetch product pages
    $sql = "SELECT id FROM products ORDER BY id ASC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $productUrl = 'https://khodiyarlab.com/product/' . $row['id'];
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($productUrl) . '</loc>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';
    echo $xml;

} catch (Exception $e) {
    error_log("Sitemap generation error: " . $e->getMessage());

    // Output error as valid XML
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<error>';
    echo '<message>' . htmlspecialchars($e->getMessage()) . '</message>';
    echo '</error>';
}
?>
