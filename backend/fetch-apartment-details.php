<?php
require_once "db_connect.php";

if (!isset($_GET["apartment_id"])) {
    echo json_encode(["error" => "Apartment ID is required."]);
    exit;
}

$apartmentID = intval($_GET["apartment_id"]);

$sql = "SELECT unit_id, apartment_id, unit_number, total_floors, living_area, bedroom_count, tb_count,
        balcony, parking_space, pet_friendly, lease_term, rent_price, month_deposit, month_advance, availability_status,
        furnished_status FROM apartment_units WHERE apartment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $apartmentID);
$stmt->execute();
$result = $stmt->get_result();

$apartmentUnits = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $apartmentUnits[] = $row;
    }
}

echo json_encode($apartmentUnits);

$stmt->close();
$conn->close();
?>