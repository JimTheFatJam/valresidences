<?php
require_once "db_connect.php";

// Check if a specific apartment ID is requested
if (isset($_POST['apartmentId'])) {
    $apartmentId = intval($_POST['apartmentId']);

    // Get apartment details
    $stmt = $conn->prepare("SELECT apartment_id, address, subdivision_address, apartment_type, total_units, units_occupied, units_vacant, map_address, date_listed FROM apartment_listings WHERE apartment_id = ?");
    $stmt->bind_param("i", $apartmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $apartment = $result->fetch_assoc();

    if ($apartment) {
        // Get apartment images
        $imgStmt = $conn->prepare("SELECT file_link FROM apartment_images WHERE apartment_id = ?");
        $imgStmt->bind_param("i", $apartmentId);
        $imgStmt->execute();
        $imgResult = $imgStmt->get_result();
        $images = [];
        while ($img = $imgResult->fetch_assoc()) {
            $images[] = $img;
        }

        $apartment['images'] = $images;

        echo json_encode([
            "success" => true,
            "data" => $apartment
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Apartment not found."
        ]);
    }

    $stmt->close();
    $imgStmt->close();
} else {
    // If no specific ID, return all apartments
    $sql = "SELECT apartment_id, address, subdivision_address, apartment_type, total_units, units_occupied, units_vacant, map_address, date_listed FROM apartment_listings";
    $result = $conn->query($sql);

    $apartments = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $apartments[] = $row;
        }
    }

    echo json_encode($apartments);
}

$conn->close();
?>