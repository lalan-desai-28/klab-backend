<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection
include "../validateSession.php"; // Include session validation

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Get the product ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or missing product ID."]);
    exit;
}

$productID = (int) $_GET['id'];

// Fetch product images from the database
$stmt = $conn->prepare("SELECT mainImagePath, subImagesOnePath, subImagesTwoPath, subImagesThreePath, subImagesFourPath, subImagesFivePath 
                        FROM products WHERE id = ?");
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Product not found."]);
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Delete images from the server
function deleteImage($imagePath) {
    $filePath = "../../" . $imagePath; // Adjust the path
    if (!empty($imagePath) && file_exists($filePath)) {
        unlink($filePath);
    }
}

// Remove all images associated with the product
deleteImage($product['mainImagePath']);
deleteImage($product['subImagesOnePath']);
deleteImage($product['subImagesTwoPath']);
deleteImage($product['subImagesThreePath']);
deleteImage($product['subImagesFourPath']);
deleteImage($product['subImagesFivePath']);

// Delete the product from the database
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $productID);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Product deleted successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to delete product."]);
}

$stmt->close();
$conn->close();
?>
