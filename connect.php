<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$servername = "localhost";
$username   = "zoqiebvw_admin";
$password   = "=(7e?UTzW6C(";
$database   = "zoqiebvw_khodiyar_lab";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}