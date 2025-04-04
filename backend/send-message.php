<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor directory

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["result" => -1, "message" => "Invalid request method."]);
    exit;
}

// Retrieve and sanitize input
$messageEmail = filter_var(trim($_POST["messageEmail"]), FILTER_SANITIZE_EMAIL);
$messageName = filter_var(trim($_POST["messageName"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$messageBody = filter_var(trim($_POST["messageBody"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validate input fields
if (empty($messageEmail) || empty($messageName) || empty($messageBody)) {
    echo json_encode(["result" => -1, "message" => "All fields are required."]);
    exit;
}

// Validate email format
if (!filter_var($messageEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["result" => -1, "message" => "Invalid email format."]);
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Change to your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplyvalresidences@gmail.com'; // Replace with your email
    $mail->Password   = 'uiwv ifsm arbk kfek'; // Use an App Password, not your real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($messageEmail, $messageName);
    $mail->addAddress('valresidences@gmail.com');

    $mail->Subject = "New Contact Form Message from " . $messageName;
    $mail->Body    = "Name: $messageName\nEmail: $messageEmail\n\nMessage:\n$messageBody\n";

    $mail->send();
    echo json_encode(["result" => 1, "message" => "Message sent successfully!"]);
} catch (Exception $e) {
    error_log("Failed to send email: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send the message. Please try again later."]);
}
exit;
