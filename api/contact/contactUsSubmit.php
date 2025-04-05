<?php
header('Content-Type: application/json');

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

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // Responsive HTML email for the user
    $userSubject = "We have received your message - Khodiyar Lab";
    $userBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .content {
            padding: 25px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f1f1f1;
        }
        .footer img {
            width: 100px;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .content, .header, .footer {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Thank You for Contacting Us</h2>
    </div>
    <div class="content">
        <p>Dear $name,</p>
        <p>Thank you for reaching out to us. We have received your message and will get in touch with you shortly.</p>
        <p>We appreciate your interest in Khodiyar Lab.</p>
        <p><strong>Your Message Summary:</strong></p>
        <ul>
            <li><strong>Subject:</strong> $subject</li>
            <li><strong>Message:</strong> $message</li>
        </ul>
        <p>Best Regards,<br/>Khodiyar Lab Team</p>
    </div>
    <div class="footer">
        <p>Follow us for more updates</p>
        <img src="https://khodiyarlab.com/logo.png" alt="Khodiyar Lab Logo">
    </div>
</div>
</body>
</html>
HTML;

    $userHeaders = "MIME-Version: 1.0\r\n";
    $userHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
    $userHeaders .= "From: donotreply@khodiyarlab.com\r\n";
    $userHeaders .= "Reply-To: donotreply@khodiyarlab.com\r\n";

    mail($email, $userSubject, $userBody, $userHeaders);

    // Responsive HTML email for admin
    $adminSubject = "New Query Received | $name | $subject";
    $adminBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container {
            background: #fff; padding: 20px; margin: auto;
            max-width: 600px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 { color: #333; }
        p { line-height: 1.6; }
    </style>
</head>
<body>
<div class="container">
    <h2>New Contact Us Submission</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Subject:</strong> $subject</p>
    <p><strong>Message:</strong></p>
    <p>$message</p>
    <hr/>
    <p>Please respond to this query as soon as possible.</p>
</div>
</body>
</html>
HTML;

    $adminHeaders = "MIME-Version: 1.0\r\n";
    $adminHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
    $adminHeaders .= "From: admin@khodiyarlab.com\r\n";
    $adminHeaders .= "Reply-To: $email\r\n";

    $adminEmails = ["desailalan02@gmail.com"];
    foreach ($adminEmails as $adminEmail) {
        mail($adminEmail, $adminSubject, $adminBody, $adminHeaders);
    }

    echo json_encode(["status" => "success", "message" => "Message received. We will contact you soon."]);
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
