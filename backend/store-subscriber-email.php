<?php
require_once "db_connect.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["subscriber_email"]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["result" => 0]);
        exit;
    }

    // Insert email into database
    $stmt = $conn->prepare("INSERT INTO subscriber_emails (email, created_at) VALUES (?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo json_encode(["result" => 1]);
    } else {
        echo json_encode(["result" => -1]);
    }

    $stmt->close();
    $conn->close();
}
?>