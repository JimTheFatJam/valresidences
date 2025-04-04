<?php
require_once "db_connect.php";

$sql = "SELECT apartment_id, address, subdivision_address, apartment_type, total_units, units_occupied, units_vacant, map_address, date_listed FROM apartment_listings";
$result = $conn->query($sql);

$apartments = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $apartments[] = $row;
    }
}

echo json_encode($apartments);

$conn->close();
?>