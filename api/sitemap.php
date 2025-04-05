<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/sitemap-error.log');

header("Content-type: text/xml");

include_once(__DIR__ . '/../../connect.php');

echo "Hello";
?>
