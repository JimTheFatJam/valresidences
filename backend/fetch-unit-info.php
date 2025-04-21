<?php
require_once "db_connect.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$unitId = $data['unitId'];

$sql = "SELECT au.unit_id, au.unit_number, au.apartment_id, al.subdivision_address
        FROM apartment_units au
        JOIN apartment_listings al ON au.apartment_id = al.apartment_id
        WHERE au.unit_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $unitId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "unit_id" => $row['unit_id'],
        "unit_number" => $row['unit_number'],
        "subdivision_address" => $row['subdivision_address']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Unit not found."]);
}

$stmt->close();
$conn->close();
?>