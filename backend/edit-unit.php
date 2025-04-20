<?php
require_once "db_connect.php";
header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unitId = $_POST['unitId'];
    $unitNumber = $_POST['unitNumber'];
    $floorCount = $_POST['floorCount'];
    $livingArea = $_POST['livingArea'];
    $bedroomCount = $_POST['bedroomCount'];
    $tbCount = $_POST['tbCount'];
    $balcony = $_POST['balconyBool'];
    $parkingSpace = $_POST['parkingSpaceBool'];
    $petFriendly = $_POST['petFriendlyBool'];
    $leaseTerm = $_POST['leaseTerm'];
    $rentPrice = $_POST['rentPrice'];
    $monthDeposit = $_POST['monthDeposit'];
    $monthAdvance = $_POST['monthAdvance'];
    $availabilityStatus = $_POST['availabilityStatus'];
    $furnishingStatus = $_POST['furnishingStatus'];

    // Initialize $images in case no files are uploaded
    $images = isset($_FILES['unitImages']) ? $_FILES['unitImages'] : null;

    // Step 1: Get apartment_id using unit_id
    $getAptSql = "SELECT apartment_id FROM apartment_units WHERE unit_id = ?";
    $aptStmt = $conn->prepare($getAptSql);
    $aptStmt->bind_param("i", $unitId);
    $aptStmt->execute();
    $aptResult = $aptStmt->get_result();

    if ($aptResult->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Apartment not found for this unit."]);
        exit;
    }

    $apartmentId = $aptResult->fetch_assoc()['apartment_id'];
    $aptStmt->close();

    // Step 2: Update unit details
    $updateSql = "UPDATE apartment_units SET 
        unit_number = ?, 
        total_floors = ?, 
        living_area = ?, 
        bedroom_count = ?, 
        tb_count = ?, 
        balcony = ?, 
        parking_space = ?, 
        pet_friendly = ?, 
        lease_term = ?, 
        rent_price = ?, 
        month_deposit = ?, 
        month_advance = ?, 
        availability_status = ?, 
        furnished_status = ? 
        WHERE unit_id = ?";

    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("iidiiiiiidiissi", 
        $unitNumber,
        $floorCount,
        $livingArea,
        $bedroomCount,
        $tbCount,
        $balcony,
        $parkingSpace,
        $petFriendly,
        $leaseTerm,
        $rentPrice,
        $monthDeposit,
        $monthAdvance,
        $availabilityStatus,
        $furnishingStatus,
        $unitId
    );

    if ($stmt->execute()) {
        // Step 3: Handle image uploads
        if ($images && !empty($images['name'][0])) {
            // Get existing images
            $sql = "SELECT unit_image_id, file_link FROM unit_images WHERE apartment_id = ? AND unit_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $apartmentId, $unitNumber);
            $stmt->execute();
            $result = $stmt->get_result();

            // Delete old images from database and directory
            while ($row = $result->fetch_assoc()) {
                $filePath = "../uploads/unit_images/" . basename($row['file_link']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $deleteSql = "DELETE FROM unit_images WHERE unit_image_id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $row['unit_image_id']);
                $deleteStmt->execute();
            }

            // Step 4: Insert new images
            foreach ($images['tmp_name'] as $key => $tmpName) {
                $fileName = "apartment{$apartmentId}unit{$unitNumber}." . ($key + 1) . ".jpg";  // Name format
                $filePath = "../uploads/unit_images/" . $fileName;

                // Move file from temp location to desired directory
                if (move_uploaded_file($tmpName, $filePath)) {
                    $insertSql = "INSERT INTO unit_images (apartment_id, unit_number, file_link) VALUES (?, ?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $fileLink = "/uploads/unit_images/" . $fileName;
                    $insertStmt->bind_param("iis", $apartmentId, $unitNumber, $fileLink);
                    $insertStmt->execute();
                }
            }
        }

        echo json_encode(["success" => true, "message" => "Unit updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating unit details."]);
    }

    $stmt->close();
    $conn->close();
}
?>