<?php
header('Content-Type: application/json');
include "../../connect.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // Send confirmation email to user
    $userSubject = "We have received your message - Khodiyar Lab";
    $userBody    = "Dear $name,\n\nThank you for contacting us.\n\nWe have received your message and will get in touch with you shortly.\n\nBest Regards,\nKhodiyar Lab";
    $userHeaders = "From: donotreply@khodiyarlab.com\r\nReply-To: donotreply@khodiyarlab.com";

    mail($email, $userSubject, $userBody, $userHeaders);

    // Send email to admin with query details
    $adminEmails  = ["desailalan02@gmail.com"]; // Add all admin emails
    $adminSubject = "New Query Received | $name | $subject";
    $adminBody    = "A new message has been received from the Contact Us form:\n\n"
        . "Name: $name\n"
        . "Email: $email\n"
        . "Subject: $subject\n"
        . "Message:\n$message\n\n"
        . "Please respond to this query as soon as possible.";

    $adminHeaders = "From: admin@khodiyarlab.com\r\nReply-To: $email";

    foreach ($adminEmails as $adminEmail) {
        mail($adminEmail, $adminSubject, $adminBody, $adminHeaders);
    }

    echo json_encode(["status" => "success", "message" => "Message received. We will contact you soon."]);
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
