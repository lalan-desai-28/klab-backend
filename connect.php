
<?php

function cors() {
    $allowed_origins = [
       "https://khodiyarlab.com",
        "https://www.khodiyarlab.com",
        "http://khodiyarlab.com",
        "http://www.khodiyarlab.com",
        "http://localhost:8080",
        "http://localhost:4173",
    ];

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $origin = rtrim($_SERVER['HTTP_ORIGIN'], '/'); // Ensure uniform comparison

        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400"); // Cache for 1 day
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        http_response_code(204); // No Content
        exit();
    }
}

cors();

$servername = "localhost";
$username   = "zoqiebvw_admin";
$password   = "=(7e?UTzW6C(";
$database   = "zoqiebvw_khodiyar_lab";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}