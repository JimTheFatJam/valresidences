<?php
require_once "../backend/db_connect.php";

$sql = "SELECT email FROM subscriber_emails";
$result = $conn->query($sql);

$emails = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

$conn->close();

echo json_encode($emails);
?>