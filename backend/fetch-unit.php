<?php
require_once "db_connect.php";
header('Content-Type: application/json');

try {
    if (!isset($_POST['unitId'])) {
        throw new Exception("Missing unitId.");
    }

    $unitId = $_POST['unitId'];

    // Fetch unit details
    $stmt = $conn->prepare("SELECT * FROM apartment_units WHERE unit_id = ?");
    $stmt->bind_param("i", $unitId);
    $stmt->execute();
    $result = $stmt->get_result();
    $unitData = $result->fetch_assoc();
    $stmt->close();

    if (!$unitData) {
        throw new Exception("Unit not found.");
    }

    // Fetch associated images
    $stmt = $conn->prepare("SELECT file_link FROM unit_images WHERE apartment_id = ? AND unit_number = ?");
    $stmt->bind_param("ii", $unitData['apartment_id'], $unitData['unit_number']);
    $stmt->execute();
    $imagesResult = $stmt->get_result();
    $images = [];

    while ($row = $imagesResult->fetch_assoc()) {
        $images[] = $row;
    }

    $stmt->close();

    echo json_encode([
        "success" => true,
        "data" => array_merge($unitData, ["images" => $images])
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>