<?php
header('Content-Type: application/json'); // JSON response
include "../../connect.php"; // Database connection
include "../validateSession.php"; // Validate request

// Define upload directory
$uploadDir = "../../uploads/"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve text input
    $title         = trim($_POST['title'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $mainCategory  = trim($_POST['mainCategory'] ?? '');
    $isANewProduct = isset($_POST['isANewProduct']) ? 1 : 0;

    // Validate required fields
    $errors = [];
    if (empty($title))         $errors[] = "Title is required.";
    if (empty($description))   $errors[] = "Description is required.";
    if (empty($mainCategory))  $errors[] = "Main category is required.";
    if (empty($_FILES['mainImage']['name'])) $errors[] = "Main image is required.";

    if (!empty($errors)) {
        header("HTTP/1.1 422");
        echo json_encode(["status" => "error", "message" => $errors]);
        exit;
    }

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload main image (Required)
    $mainImagePath = uploadImage($_FILES['mainImage'], $uploadDir);
    if (!$mainImagePath) {
        header("HTTP/1.1 400");
        echo json_encode(["status" => "error", "message" => "Main image upload failed."]);
        exit;
    }

    // Upload optional images (Only if provided)
    $subImagesOnePath   = !empty($_FILES['subImagesOne']['name']) ? uploadImage($_FILES['subImagesOne'], $uploadDir) : null;
    $subImagesTwoPath   = !empty($_FILES['subImagesTwo']['name']) ? uploadImage($_FILES['subImagesTwo'], $uploadDir) : null;
    $subImagesThreePath = !empty($_FILES['subImagesThree']['name']) ? uploadImage($_FILES['subImagesThree'], $uploadDir) : null;
    $subImagesFourPath  = !empty($_FILES['subImagesFour']['name']) ? uploadImage($_FILES['subImagesFour'], $uploadDir) : null;
    $subImagesFivePath  = !empty($_FILES['subImagesFive']['name']) ? uploadImage($_FILES['subImagesFive'], $uploadDir) : null;

    // Prepare SQL query (Removed subCategory)
    $sql = "INSERT INTO products (title, mainImagePath, description, mainCategory, isANewProduct,
            subImagesOnePath, subImagesTwoPath, subImagesThreePath, subImagesFourPath, subImagesFivePath)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssisisss",
            $title, $mainImagePath, $description, $mainCategory, $isANewProduct,
            $subImagesOnePath, $subImagesTwoPath, $subImagesThreePath, $subImagesFourPath, $subImagesFivePath
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Product added successfully."]);
        } else {
            header("HTTP/1.1 502");
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error in SQL preparation."]);
    }

    $conn->close();
} else {
    header("HTTP/1.1 422");
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

// Function to handle image upload
function uploadImage($imageFile, $targetDir)
{
    if (!empty($imageFile['name'])) {
        $fileName       = uniqid() . "_" . basename($imageFile["name"]);
        $targetFilePath = $targetDir . $fileName; // Corrected file path

        // Check file type
        $fileType     = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($imageFile["tmp_name"], $targetFilePath)) {
                return "uploads/" . $fileName; // Return relative path for DB
            } else {
                return null; // Upload failed
            }
        } else {
            return null; // Invalid file type
        }
    }
    return null; // No file uploaded
}
