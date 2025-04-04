<?php
require_once "../backend/db_connect.php";

if (isset($_GET["apartment_id"]) && isset($_GET["unit_number"])) {
    $apartmentID = $_GET["apartment_id"];
    $unitNumber = $_GET["unit_number"];

    $stmt = $conn->prepare("SELECT file_link FROM unit_images WHERE apartment_id = ? AND unit_number = ?");
    $stmt->bind_param("ii", $apartmentID, $unitNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];

    while ($row = $result->fetch_assoc()) {
        $images[] = $row['file_link'];
    }

    echo json_encode($images);

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>
