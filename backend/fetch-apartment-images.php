<?php
require_once '../backend/db_connect.php';

if (isset($_GET['apartment_id'])) {
    $apartment_id = $_GET['apartment_id'];


    $stmt = $conn->prepare("SELECT file_link FROM apartment_images WHERE apartment_id = ?");
    $stmt->bind_param("i", $apartment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];

    while ($row = $result->fetch_assoc()) {
        $images[] = $row['file_link'];
    }

    echo json_encode($images);

    $stmt->close();
} else {
    echo json_encode(["error" => "No apartment_id provided"]);
}

$conn->close();
?>