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
$unitId = filter_var(trim($_POST["unitId"]), FILTER_SANITIZE_NUMBER_INT);
$subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
$message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
    $mail->Subject = "New Inquiry for Unit: " . $subject;
    $mail->Body    = "
        Unit ID: $unitId\n
        Subject: $subject\n
        Email: $email\n\n
        Message:\n
        $message
    ";

    // Send the email
    $mail->send();
    echo json_encode(["result" => 1, "message" => "Inquiry sent successfully!"]);
} catch (Exception $e) {
    error_log("Failed to send email: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send the inquiry. Please try again later."]);
}

exit;
?>