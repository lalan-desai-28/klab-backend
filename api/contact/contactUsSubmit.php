<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = nl2br(trim($_POST['message'] ?? '')); // Convert new lines to <br> for HTML

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

    // ✅ Responsive HTML email for the customer
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: 20px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        .content ul {
            padding-left: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f1f1f1;
        }
        .footer img {
            width: 150px;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .content, .header, .footer {
                padding: 15px;
            }
            .footer img {
                width: 120px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        Thank You for Contacting Us
    </div>
    <div class="content">
        <p>Dear <strong>$name</strong>,</p>
        <p>Thank you for reaching out to us. We have received your message and will get in touch with you shortly.</p>
        <p>We appreciate your interest in Khodiyar Lab.</p>
        <p><strong>Your Message Summary:</strong></p>
        <ul>
            <li><strong>Subject:</strong> $subject</li>
            <li><strong>Message:</strong> <br><br> $message</li>
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

    // ✅ Responsive HTML email for admin
    $adminSubject = "New Query Received | $name | $subject";
    $adminBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            background-color: #0073e6;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>New Contact Us Submission</h2>
    <div class="content">
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong> <br><br> $message</p>
    </div>
    <div class="footer">
        Please respond to this query as soon as possible.
    </div>
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
