<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor directory
require_once "../backend/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["result" => -1, "message" => "Invalid request method."]);
    exit;
}

// Retrieve and sanitize input
$subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$messageBody = filter_var(trim($_POST["message"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (empty($subject) || empty($messageBody)) {
    echo json_encode(["result" => -1, "message" => "Subject and message cannot be empty."]);
    exit;
}

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["result" => -1, "message" => "Database connection failed."]);
    exit;
}

// GET SUBSCRIBER EMAILS FOR NOW, CHANGE TO TENANT EMAILS LATER
$sql = "SELECT email FROM subscriber_emails";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["result" => -1, "message" => "No subscribers found."]);
    exit;
}

$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = $row['email'];
}
$conn->close();

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Change to your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplyvalresidences@gmail.com'; // Replace with your email
    $mail->Password   = 'uiwv ifsm arbk kfek'; // Use an App Password, not your real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('noreplyvalresidences@gmail.com', 'Val Residences');
    foreach ($emails as $email) {
        $mail->addBCC($email);
    }

    $mail->Subject = $subject;
    $mail->Body    = $messageBody;

    $mail->send();
    echo json_encode(["result" => 1, "message" => "Vacancy announcement sent successfully!"]);
} catch (Exception $e) {
    error_log("Failed to send email: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send announcement."]);
}
exit;