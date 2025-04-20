<?php
require_once "db_connect.php";
header('Content-Type: application/json');

try {
    // 1. Get posted data
    $apartmentId = $_POST['apartmentId'];
    $unitNumber = $_POST['unitNumber'];
    $bedroomCount = $_POST['bedroomCount'];
    $rentPrice = $_POST['rentPrice'];
    $floorCount = $_POST['floorCount'];
    $tbCount = $_POST['tbCount'];
    $monthAdvance = $_POST['monthAdvance'];
    $livingArea = $_POST['livingArea'];
    $leaseTerm = $_POST['leaseTerm'];
    $monthDeposit = $_POST['monthDeposit'];
    $availabilityStatus = $_POST['availabilityStatus'];
    $furnishingStatus = $_POST['furnishingStatus'];
    $parkingSpaceBool = $_POST['parkingSpaceBool'];
    $petFriendlyBool = $_POST['petFriendlyBool'];
    $balconyBool = $_POST['balconyBool'];

    // 2. Insert into apartment_listings
    $stmt = $conn->prepare("INSERT INTO apartment_units (
        apartment_id,
        unit_number,
        total_floors,
        living_area,
        bedroom_count,
        tb_count,
        balcony,
        parking_space,
        pet_friendly,
        lease_term,
        rent_price,
        month_deposit,
        month_advance,
        availability_status,
        furnished_status,
        created_at,
        updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("iiidiiiiiidiiss",
        $apartmentId,
        $unitNumber,
        $floorCount,
        $livingArea,
        $bedroomCount,
        $tbCount,
        $balconyBool,
        $parkingSpaceBool,
        $petFriendlyBool,
        $leaseTerm,
        $rentPrice,
        $monthDeposit,
        $monthAdvance,
        $availabilityStatus,
        $furnishingStatus
    );
    $stmt->execute();
    $stmt->close();

    // 3. Handle image uploads
    $uploadDir = __DIR__ . '/../uploads/unit_images/';
    $webPathPrefix = '/uploads/unit_images/';
    $imageCount = count($_FILES['unitImages']['name']);

    for ($i = 0; $i < $imageCount; $i++) {
        $tmpName = $_FILES['unitImages']['tmp_name'][$i];
        $extension = pathinfo($_FILES['unitImages']['name'][$i], PATHINFO_EXTENSION);
        $newFileName = "apartment{$apartmentId}unit{$unitNumber}." . ($i + 1) . "." . $extension;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $fileLink = $webPathPrefix . $newFileName;

            // 4. Insert image path into apartment_images table
            $stmt = $conn->prepare("INSERT INTO unit_images (apartment_id, unit_number, file_link) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $apartmentId, $unitNumber, $fileLink);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "Unit and images uploaded successfully!",
        "apartment_id" => $apartmentId
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Upload failed: " . $e->getMessage()
    ]);
}
?>