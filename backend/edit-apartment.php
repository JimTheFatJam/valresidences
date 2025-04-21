<?php
require_once "db_connect.php";

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apartmentId = $_POST['apartmentId'];
    $subdivisionAddress = $_POST['subdivisionAddress'];
    $address = $_POST['address'];
    $type = $_POST['type'];
    $mapURL = $_POST['mapURL'];

    // Initialize $images in case no files are uploaded
    $images = isset($_FILES['apartmentImages']) ? $_FILES['apartmentImages'] : null;

    // Update apartment details in apartment_listings table
    $sql = "UPDATE apartment_listings SET subdivision_address = ?, address = ?, apartment_type = ?, map_address = ? WHERE apartment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $subdivisionAddress, $address, $type, $mapURL, $apartmentId);

    if ($stmt->execute()) {
        // Handle image uploads (delete old images, if necessary, and insert new ones)
        if ($images && !empty($images['name'][0])) {  // If new images are uploaded
            // Fetch existing images from the database
            $sql = "SELECT apartment_image_ID, file_link FROM apartment_images WHERE apartment_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $apartmentId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Delete old images from database and directory
            while ($row = $result->fetch_assoc()) {
                $filePath = "../uploads/apartment_images/" . basename($row['file_link']);
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete image file
                }
                $deleteSql = "DELETE FROM apartment_images WHERE apartment_image_ID = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $row['apartment_image_ID']);
                $deleteStmt->execute();
            }

            // Insert new images into apartment_images table
            foreach ($images['tmp_name'] as $key => $tmpName) {
                $extension = strtolower(pathinfo($images['name'][$key], PATHINFO_EXTENSION)); // Get original extension
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Define allowed extensions

                // Validate file extension
                if (in_array($extension, $allowedExtensions)) {
                    $fileName = "apartment" . $apartmentId . "." . ($key + 1) . "." . $extension; // Use dynamic extension
                    $filePath = "../uploads/apartment_images/" . $fileName;

                    // Move file from temp location to desired directory
                    if (move_uploaded_file($tmpName, $filePath)) {
                        // Prepare and execute SQL insert
                        $insertSql = "INSERT INTO apartment_images (apartment_id, file_link) VALUES (?, ?)";
                        $insertStmt = $conn->prepare($insertSql);
                        $fileLink = "/uploads/apartment_images/" . $fileName;
                        $insertStmt->bind_param("is", $apartmentId, $fileLink);
                        if (!$insertStmt->execute()) {
                            // Handle database insert error
                            echo "Error inserting image into database: " . $insertStmt->error;
                        }
                    } else {
                        // Handle file upload error
                        echo "Error uploading file: " . $images['name'][$key];
                    }
                } else {
                    // Handle invalid file type
                    echo "Invalid file type: " . $images['name'][$key];
                }
            }
        }

        echo json_encode(["success" => true, "message" => "Apartment updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating apartment details."]);
    }

    $stmt->close();
    $conn->close();
}
?>