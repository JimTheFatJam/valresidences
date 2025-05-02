<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor directory
require_once '../backend/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["result" => -1, "message" => "Invalid request method."]);
    exit;
}

// Retrieve and sanitize input
$unitId = filter_var(trim($_POST["unitId"]), FILTER_SANITIZE_NUMBER_INT);
$subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);


// Get user_id from login_users where user_email = $email
$sql = "SELECT user_id FROM login_users WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["result" => -1, "message" => "User not found."]);
    exit;
}

$user = $result->fetch_assoc();
$userId = $user['user_id'];

// Insert application into tenant_applications
$insert = $conn->prepare("INSERT INTO tenant_applications (unit_id, user_id, application_status) VALUES (?, ?, 'pending')");
$insert->bind_param("ii", $unitId, $userId);

if (!$insert->execute()) {
    echo json_encode(["result" => -1, "message" => "Failed to save application to database."]);
    exit;
}

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Change to your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplyvalresidences@gmail.com'; // Replace with your email
    $mail->Password   = 'uiwv ifsm arbk kfek'; // Use an App Password, not your real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender's email address and name
    $mail->setFrom($email, $subject);

    // Receiver's email address
    $mail->addAddress('valresidences@gmail.com');

    // Email subject and body content
    $mail->Subject = "New Application for Unit: " . $subject;
    $mail->Body    = "
        Unit ID: $unitId\n
        Subject: $subject\n
        Email: $email\n
    ";

    // Send the email
    $mail->send();
    echo json_encode(["result" => 1, "message" => "Application sent successfully!"]);
} catch (Exception $e) {
    error_log("Failed to send email: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send your application. Please try again later."]);
}

exit;
?>