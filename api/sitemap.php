<?php
header("Content-type: text/xml");
include "../../connect.php";

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Main Pages
$pages = [
    'https://khodiyarlab.com/',
    'https://khodiyarlab.com/products',
];

foreach ($pages as $page) {
    echo "<url><loc>$page</loc><priority>1.0</priority><changefreq>daily</changefreq></url>";
}

// Fetch product URLs from database
$sql = "SELECT id FROM products ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productId = $row['id'];
        echo "<url><loc>https://khodiyarlab.com/product/$productId</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>";
    }
}

echo '</urlset>';
?>
