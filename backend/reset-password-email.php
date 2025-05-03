<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["result" => -1, "message" => "Invalid request method."]);
    exit;
}

// Sanitize and validate input
$resetEmail = filter_var(trim($_POST["resetEmail"]), FILTER_SANITIZE_EMAIL);

if (empty($resetEmail)) {
    echo json_encode(["result" => -1, "message" => "Email is required."]);
    exit;
}

if (!filter_var($resetEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["result" => -1, "message" => "Invalid email format."]);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT user_id FROM login_users WHERE user_email = ?");
$stmt->bind_param("s", $resetEmail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    echo json_encode(["result" => -1, "message" => "Email not found."]);
    exit;
}
$stmt->close();

// Generate token and expiry
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Delete previous tokens if any
$stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
$stmt->bind_param("s", $resetEmail);
$stmt->execute();
$stmt->close();

// Insert new reset request
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $resetEmail, $token, $expires);
$stmt->execute();
$stmt->close();

// Send reset email
$resetLink = "valresidences.dcism.org/pages/reset-password.php?token=" . $token; //CHANGE

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplyvalresidences@gmail.com';
    $mail->Password   = 'uiwv ifsm arbk kfek'; // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('noreplyvalresidences@gmail.com', 'Val Residences');
    $mail->addAddress($resetEmail);

    $mail->Subject = 'Reset Your Password - Val Residences';
    $mail->Body    = "Hello,\n\nYou requested to reset your password. Click the link below:\n\n$resetLink\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, you can safely ignore this email.";

    $mail->send();
    echo json_encode(["result" => 1, "message" => "Password reset link sent successfully."]);
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send email. Please try again later."]);
}
exit;