<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection

// Get search parameters from POST request
$search       = trim($_POST['search'] ?? '');
$mainCategory = trim($_POST['mainCategory'] ?? '');
$page         = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$offset       = isset($_POST['offset']) ? (int) $_POST['offset'] : 10;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $offset;

// Base query
$sql = "SELECT id, title, mainImagePath, description, mainCategory, isANewProduct
        FROM products WHERE 1=1";

$params = [];
$types  = '';

if (! empty($search)) {
    $sql .= " AND (title LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%'))";
    $params[] = &$search;
    $params[] = &$search;
    $types .= "ss";
}

if (! empty($mainCategory)) {
    $sql .= " AND mainCategory = ?";
    $params[] = &$mainCategory;
    $types .= "s";
}

// Add pagination
$sql .= " ORDER BY id DESC LIMIT ?, ?";
$params[] = &$start;
$params[] = &$offset;
$types .= "ii";

// Prepare statement
$stmt = $conn->prepare($sql);

if (! empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode([
    "status" => "success",
    "page"   => $page,
    "offset" => $offset,
    "data"   => $products,
]);
