<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Fetch last 5 newly added products
    $sql = "SELECT id, title, mainImagePath, description, mainCategory, createdAt 
            FROM products 
            ORDER BY createdAt DESC 
            LIMIT 5";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode(["status" => "success", "products" => $products]);
    } else {
        echo json_encode(["status" => "success", "products" => []]); // No products found
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
