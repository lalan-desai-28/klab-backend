<?php
header('Content-Type: application/json');
include "../../connect.php";         // Database connection

// Check if ID is provided
if (! isset($_GET['id']) || ! is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
    exit;
}

$productID = (int) $_GET['id'];

// Prepare SQL query
$sql = "SELECT id, title, mainImagePath, description, mainCategory, isANewProduct,
               subImagesOnePath, subImagesTwoPath, subImagesThreePath, subImagesFourPath, subImagesFivePath
        FROM products WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Product not found."]);
} else {
    $product = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $product]);
}

$stmt->close();
$conn->close();
