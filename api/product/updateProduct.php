<?php
header('Content-Type: application/json'); // JSON response
include "../../connect.php"; // Database connection
include "../validateSession.php"; // Validate request

$uploadDir = "../../uploads/"; // Upload directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and validate product ID
    $id = $_POST['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid or missing product ID."]);
        exit;
    }

    // Retrieve product details
    $stmt = $conn->prepare("SELECT mainImagePath, subImagesOnePath, subImagesTwoPath, subImagesThreePath, subImagesFourPath, subImagesFivePath FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }
    $existingProduct = $result->fetch_assoc();
    $stmt->close();

    // Retrieve input values
    $title         = trim($_POST['title'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $mainCategory  = trim($_POST['mainCategory'] ?? '');
    $isANewProduct = isset($_POST['isANewProduct']) ? 1 : 0;

    // Initialize update fields
    $updateFields = [];
    $updateParams = [];
    $paramTypes   = '';

    if (!empty($title)) {
        $updateFields[] = "title = ?";
        $updateParams[] = $title;
        $paramTypes .= 's';
    }

    if (!empty($description)) {
        $updateFields[] = "description = ?";
        $updateParams[] = $description;
        $paramTypes .= 's';
    }

    if (!empty($mainCategory)) {
        $updateFields[] = "mainCategory = ?";
        $updateParams[] = $mainCategory;
        $paramTypes .= 's';
    }

    $updateFields[] = "isANewProduct = ?";
    $updateParams[] = $isANewProduct;
    $paramTypes .= 'i';

    // Handle images
    $imageFields = [
        "mainImage" => "mainImagePath",
        "subImagesOne" => "subImagesOnePath",
        "subImagesTwo" => "subImagesTwoPath",
        "subImagesThree" => "subImagesThreePath",
        "subImagesFour" => "subImagesFourPath",
        "subImagesFive" => "subImagesFivePath"
    ];

    foreach ($imageFields as $inputName => $dbField) {
        if (!empty($_FILES[$inputName]['name'])) {
            $newImagePath = uploadImage($_FILES[$inputName], $uploadDir);

            if ($newImagePath) {
                // Delete old image if exists
                if (!empty($existingProduct[$dbField]) && file_exists("../../" . $existingProduct[$dbField])) {
                    unlink("../../" . $existingProduct[$dbField]);
                }

                // Update new image path
                $updateFields[] = "$dbField = ?";
                $updateParams[] = $newImagePath;
                $paramTypes .= 's';
            }
        }
    }

    // Handle image removal requests (if the user wants to delete a sub-image)
    foreach ($imageFields as $inputName => $dbField) {
        $removeKey = "remove" . ucfirst($inputName); // Example: removeSubImagesOne
        if (isset($_POST[$removeKey]) && $_POST[$removeKey] == "1") {
            // Delete old image file
            if (!empty($existingProduct[$dbField]) && file_exists("../../" . $existingProduct[$dbField])) {
                unlink("../../" . $existingProduct[$dbField]);
            }

            // Set the field to NULL in the database
            $updateFields[] = "$dbField = NULL";
        }
    }

    // If no updates, return an error
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "No fields to update."]);
        exit;
    }

    // Construct SQL query dynamically
    $sql = "UPDATE products SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $updateParams[] = $id;
    $paramTypes .= 'i';

    // Prepare and execute statement
    if ($stmt = $conn->prepare($sql)) {
        if (!empty($updateParams)) {
            $stmt->bind_param($paramTypes, ...$updateParams);
        }

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Product updated successfully."]);
        } else {
            http_response_code(502);
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error in SQL preparation."]);
    }

    $conn->close();
} else {
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
