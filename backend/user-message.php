<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require_once "../backend/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["result" => -1, "message" => "Invalid request method."]);
    exit;
}

// Sanitize input
$userStatus = $_POST["userStatus"];
$contactEmail = filter_var(trim($_POST["contactEmail"]), FILTER_SANITIZE_EMAIL);
$contactSubject = filter_var(trim($_POST["contactSubject"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$contactMessage = filter_var(trim($_POST["contactMessage"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Default fallback values
$subdivisionAddress = 'Unknown';
$unitNumber = 'Unknown';

try {
    // Get user_id
    $stmt = $conn->prepare("SELECT user_id FROM login_users WHERE user_email = ?");
    $stmt->bind_param("s", $contactEmail);
    $stmt->execute();
    $stmt->bind_result($userId);
    if (!$stmt->fetch()) {
        throw new Exception("User not found.");
    }
    $stmt->close();

    // Get unit_id
    $stmt = $conn->prepare("SELECT unit_id FROM tenant_applications WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($unitId);
    if (!$stmt->fetch()) {
        throw new Exception("Tenant application not found.");
    }
    $stmt->close();

    // Get unit_number and apartment_id
    $stmt = $conn->prepare("SELECT unit_number, apartment_id FROM apartment_units WHERE unit_id = ?");
    $stmt->bind_param("i", $unitId);
    $stmt->execute();
    $stmt->bind_result($unitNumber, $apartmentId);
    if (!$stmt->fetch()) {
        throw new Exception("Apartment unit not found.");
    }
    $stmt->close();

    // Get subdivision_address
    $stmt = $conn->prepare("SELECT subdivision_address FROM apartment_listings WHERE apartment_id = ?");
    $stmt->bind_param("i", $apartmentId);
    $stmt->execute();
    $stmt->bind_result($subdivisionAddress);
    if (!$stmt->fetch()) {
        throw new Exception("Apartment listing not found.");
    }
    $stmt->close();

    // Get name
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM login_users WHERE user_email = ?");
    $stmt->bind_param("s", $contactEmail);
    $stmt->execute();
    $stmt->bind_result($userId, $firstName, $lastName);
    $stmt->fetch();
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching user info: " . $e->getMessage());
    // Proceed without apartment info
}

// Initialize PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreplyvalresidences@gmail.com';
    $mail->Password = 'uiwv ifsm arbk kfek'; // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('noreplyvalresidences@gmail.com', "New $userStatus Message");
    $mail->addReplyTo($contactEmail);
    $mail->addAddress('valresidences@gmail.com');

    $mail->Subject = "New $userStatus Message: $contactSubject";
    $mailBody = "You received a new $userStatus message.\n\n" .
        "Sender Name: $firstName $lastName\n" .
        "Sender Email: $contactEmail\n";

    if (strtolower(string: $userStatus) !== "user") {
        $mailBody .= "Sender Address: $subdivisionAddress Unit $unitNumber\n" ;
    }

    $mailBody .= "\nSubject: $contactSubject\n\nMessage:\n$contactMessage";

    $mail->Body = $mailBody;

    $mail->send();
    echo json_encode(["result" => 1, "message" => "Message sent successfully!"]);
} catch (Exception $e) {
    error_log("Failed to send email: " . $mail->ErrorInfo);
    echo json_encode(["result" => -1, "message" => "Failed to send the message. Please try again later."]);
}
exit;