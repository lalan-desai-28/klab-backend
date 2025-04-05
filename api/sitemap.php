<?php
// Turn off error reporting on output to avoid corrupting XML
error_reporting(0);
ini_set('display_errors', 0);

// Set XML content-type header
header("Content-type: text/xml");

// Include your DB connection
include "../../connect.php";

// Start building XML string
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Static pages
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

// Fetch product links from DB
$sql = "SELECT id FROM products ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productUrl = 'https://khodiyarlab.com/product/' . $row['id'];
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($productUrl) . '</loc>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '</url>';
    }
}

// Close the root tag
$xml .= '</urlset>';

// Output the XML
echo $xml;
?>
