<?php
require_once "db_connect.php";
header('Content-Type: application/json');

try {
    // 1. Get posted data
    $subdivisionAddress = $_POST['subdivisionAddress'];
    $address = $_POST['address'];
    $type = $_POST['type'];
    $mapURL = $_POST['mapURL'];

    // 2. Insert into apartment_listings
    $stmt = $conn->prepare("INSERT INTO apartment_listings 
        (address, subdivision_address, apartment_type, total_units, units_occupied, units_vacant, map_address, date_listed)
        VALUES (?, ?, ?, 0, 0, 0, ?, NOW())");
    $stmt->bind_param("ssss", $address, $subdivisionAddress, $type, $mapURL);
    $stmt->execute();
    $apartmentId = $stmt->insert_id;
    $stmt->close();

    // 3. Handle image uploads
    $uploadDir = __DIR__ . '/../uploads/apartment_images/';
    $webPathPrefix = '/uploads/apartment_images/';
    $imageCount = count($_FILES['apartmentImages']['name']);

    for ($i = 0; $i < $imageCount; $i++) {
        $tmpName = $_FILES['apartmentImages']['tmp_name'][$i];
        $extension = pathinfo($_FILES['apartmentImages']['name'][$i], PATHINFO_EXTENSION);
        $newFileName = "apartment{$apartmentId}." . ($i + 1) . "." . $extension;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $fileLink = $webPathPrefix . $newFileName;

            // 4. Insert image path into apartment_images table
            $stmt = $conn->prepare("INSERT INTO apartment_images (apartment_id, file_link) VALUES (?, ?)");
            $stmt->bind_param("is", $apartmentId, $fileLink);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "Apartment and images uploaded successfully!",
        "apartment_id" => $apartmentId
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Upload failed: " . $e->getMessage()
    ]);
}
?>