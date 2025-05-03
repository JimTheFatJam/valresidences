<?php
session_start();
header("Content-Type: application/json");
require_once "db_connect.php";

if (!isset($_SESSION['email'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

$email = $_SESSION['email'];

// Step 1: Get user_id
$stmt = $conn->prepare("SELECT user_id FROM login_users WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    echo json_encode([]);
    exit;
}

// Step 2: Get application status info
$sql = "
    SELECT 
        ta.application_id,
        au.unit_number,
        al.subdivision_address,
        ta.application_status,
        ta.created_at
    FROM tenant_applications ta
    JOIN apartment_units au ON ta.unit_id = au.unit_id
    JOIN apartment_listings al ON au.apartment_id = al.apartment_id
    WHERE ta.user_id = ?
    ORDER BY ta.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

echo json_encode($applications);
