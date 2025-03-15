<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection

$headers      = getallheaders();
$sessionToken = $headers['Authorization'] ?? '';

if (empty($sessionToken)) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized: No session token provided."]);
    exit;
}

// Validate session token
$stmt = $conn->prepare("SELECT id FROM admin WHERE session = ?");
$stmt->bind_param("s", $sessionToken);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid session token."]);
    exit;
}

// If session is valid, continue the request
$stmt->close();
