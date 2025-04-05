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

    // Convert newlines to <br> for HTML emails
    $messageHtml = nl2br(htmlspecialchars($message));

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
            margin: 0; padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 25px;
            color: #333;
        }
        .footer {
            text-align: center;
            background-color: #f1f1f1;
            padding: 20px;
        }
        .footer img {
            max-width: 150px;
            height: auto;
        }
        @media only screen and (max-width: 600px) {
            .content, .header, .footer {
                padding: 15px;
            }
            .footer img {
                max-width: 100px;
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
        <p>Dear <strong>$name</strong>,</p>
        <p>Thank you for reaching out to us. We have received your message and will get in touch with you shortly.</p>
        <p>We appreciate your interest in Khodiyar Lab.</p>
        <p><strong>Your Message Summary:</strong></p>
        <ul>
            <li><strong>Subject:</strong> $subject</li>
            <li><strong>Message:</strong><br>$messageHtml</li>
        </ul>
        <p>Best Regards,<br/>
        Khodiyar Lab Team<br/>
        📞 +91 93742 41351<br/>
        📞 +91 98257 28503</p>
    </div>
    <div class="footer">
        <p>Powered by Khodiyar Lab</p>
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

    // Admin HTML email with improved layout
    $adminSubject = "New Query Received | $name | $subject";
    $adminBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            color: #2c3e50;
        }
        .info-label {
            font-weight: bold;
        }
        .message-box {
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📩 New Contact Us Submission</h2>
    <p><span class="info-label">Name:</span> $name</p>
    <p><span class="info-label">Email:</span> $email</p>
    <p><span class="info-label">Subject:</span> $subject</p>
    <div class="message-box">
        $messageHtml
    </div>
    <p style="margin-top: 20px;">📞 Please respond to this query as soon as possible.</p>
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
