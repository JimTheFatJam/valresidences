<?php
require_once "db_connect.php";

$sql = "
    SELECT 
        ta.application_id,
        ta.unit_id,
        ta.user_id,
        ta.application_status,
        ta.created_at,
        au.unit_number,
        au.apartment_id,
        al.subdivision_address,
        lu.first_name,
        lu.last_name,
        lu.user_email
    FROM tenant_applications ta
    JOIN apartment_units au ON ta.unit_id = au.unit_id
    JOIN apartment_listings al ON au.apartment_id = al.apartment_id
    JOIN login_users lu ON ta.user_id = lu.user_id
";

$result = $conn->query($sql);

$tenant_application = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tenant_application[] = $row;
    }
}

echo json_encode($tenant_application);

$conn->close();
?>