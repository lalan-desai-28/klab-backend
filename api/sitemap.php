<?php
// Clear any accidental output
ob_clean();
header("Content-Type: text/xml; charset=utf-8");

// For development, enable error reporting (disable display_errors in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Create DB connection here instead of including connect.php
    $servername = "localhost";
    $username   = "zoqiebvw_admin";
    $password   = "=(7e?UTzW6C(";
    $database   = "zoqiebvw_khodiyar_lab";
    
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Initialize DOMDocument for XML creation
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    
    // Create root element <urlset> with the proper namespace
    $urlset = $dom->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $dom->appendChild($urlset);
    
    // Add static pages
    $staticPages = [
        'https://khodiyarlab.com/',
        'https://khodiyarlab.com/products',
    ];
    
    foreach ($staticPages as $pageUrl) {
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $pageUrl));
        $url->appendChild($dom->createElement('priority', '1.0'));
        $url->appendChild($dom->createElement('changefreq', 'daily'));
        $urlset->appendChild($url);
    }
    
    // Add dynamic product pages from the database
    $query = "SELECT id FROM products ORDER BY id ASC";
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    while ($row = $result->fetch_assoc()) {
        $productUrl = 'https://khodiyarlab.com/product/' . $row['id'];
        
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $productUrl));
        $url->appendChild($dom->createElement('priority', '0.8'));
        $url->appendChild($dom->createElement('changefreq', 'weekly'));
        $urlset->appendChild($url);
    }
    
    // Output the XML sitemap
    echo $dom->saveXML();
    
} catch (Throwable $e) {
    // In case of error, output plain text error for debugging
    header("Content-Type: text/plain; charset=utf-8");
    echo "Sitemap generation error: " . $e->getMessage();
}