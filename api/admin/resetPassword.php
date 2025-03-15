<?php
header('Content-Type: application/json');
include "../../connect.php";         // Database connection
include "../validateSession.php"; // Include session validation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = trim($_POST['newPassword'] ?? '');

    // Validate inputs
    if (empty($newPassword)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "New password is required."]);
        exit;
    }

    // Fetch current admin details (assuming a single admin with id = 1)
    $stmt = $conn->prepare("SELECT id FROM admin WHERE id = 1");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid session or not authorized."]);
        exit;
    }
    $stmt->close();

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Remove session token and update password
    $stmt = $conn->prepare("UPDATE admin SET password = ?, session = NULL WHERE id = 1");
    $stmt->bind_param("s", $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password updated successfully. Please log in again."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to update password."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
