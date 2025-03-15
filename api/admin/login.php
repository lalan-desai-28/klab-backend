<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Username and password are required."]);
        exit;
    }

    // Get admin details
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE id = 1 AND username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
        exit;
    }

    $admin = $result->fetch_assoc();
    $stmt->close();

    // Verify password using password_verify()
    if (!password_verify($password, $admin['password'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
        exit;
    }

    // Generate new session token
    $sessionToken = bin2hex(random_bytes(32));

    // Store session token in the database
    $stmt = $conn->prepare("UPDATE admin SET session = ? WHERE id = 1");
    $stmt->bind_param("s", $sessionToken);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "session" => $sessionToken]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to create session."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
