<?php
require_once "db_connect.php";
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['unitId'])) {
    $unitId = intval($data['unitId']);

    // Step 1: Get apartment_id and unit_number from apartment_units
    $getInfoSql = "SELECT apartment_id, unit_number FROM apartment_units WHERE unit_id = ?";
    $stmt = $conn->prepare($getInfoSql);
    $stmt->bind_param("i", $unitId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $apartmentId = $row['apartment_id'];
        $unitNumber = $row['unit_number'];

        // Step 2: Fetch image file paths from unit_images
        $fetchImagesSql = "SELECT file_link FROM unit_images WHERE apartment_id = ? AND unit_number = ?";
        $fetchStmt = $conn->prepare($fetchImagesSql);
        $fetchStmt->bind_param("ii", $apartmentId, $unitNumber);
        $fetchStmt->execute();
        $imageResult = $fetchStmt->get_result();

        while ($imgRow = $imageResult->fetch_assoc()) {
            $filePath = ".." . $imgRow['file_link']; // prepend .. to get relative path to /uploads
            if (file_exists($filePath)) {
                unlink($filePath); // delete image file
            }
        }

        // Step 3: Delete image records from unit_images
        $deleteImagesSql = "DELETE FROM unit_images WHERE apartment_id = ? AND unit_number = ?";
        $deleteStmt = $conn->prepare($deleteImagesSql);
        $deleteStmt->bind_param("ii", $apartmentId, $unitNumber);
        $deleteStmt->execute();

        // Step 4: Delete the unit from apartment_units
        $deleteUnitSql = "DELETE FROM apartment_units WHERE unit_id = ?";
        $finalStmt = $conn->prepare($deleteUnitSql);
        $finalStmt->bind_param("i", $unitId);

        if ($finalStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Unit and its images deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete unit."]);
        }

        $finalStmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Unit not found."]);
    }

    $stmt->close();
    $conn->close();
}
?>