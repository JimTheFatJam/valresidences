<?php
require_once "db_connect.php";
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['apartmentId'])) {
    $apartmentId = intval($data['apartmentId']);

    try {
        // 1. Fetch file paths
        $unitImagePaths = [];
        $apartmentImagePaths = [];

        $stmt = $conn->prepare("SELECT file_link FROM unit_images WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $unitImagePaths[] = ".." . $row['file_link'];
        }

        $stmt = $conn->prepare("SELECT file_link FROM apartment_images WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $apartmentImagePaths[] = ".." . $row['file_link'];
        }

        // 2. Delete image records first
        $stmt = $conn->prepare("DELETE FROM unit_images WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM apartment_images WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();

        // 3. Delete units
        $stmt = $conn->prepare("DELETE FROM apartment_units WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();

        // 4. Delete apartment
        $stmt = $conn->prepare("DELETE FROM apartment_listings WHERE apartment_id = ?");
        $stmt->bind_param("i", $apartmentId);
        $stmt->execute();

        // 5. Delete physical image files
        foreach (array_merge($unitImagePaths, $apartmentImagePaths) as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>