<?php
include 'db_connect.php';

$apartment_id = $_GET['apartment_id'];

// Query to get the minimum and maximum rent prices for the specified apartment_id
$sql = "SELECT MIN(rent_price) AS min_price, MAX(rent_price) AS max_price FROM apartment_units WHERE apartment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $apartment_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result);
?>