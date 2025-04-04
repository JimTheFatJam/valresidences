<?php
require_once "db_connect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : "";

    if (empty($userEmail)) {
        echo json_encode(["result" => -1, "message" => "Email is required."]);
        exit;
    }

    $query = "SELECT * FROM login_users WHERE user_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(["result" => -1, "message" => "Email not found."]);
        exit;
    }

    // Generate new verification code and expiry time
    $verificationCode = bin2hex(random_bytes(16)); // Generate a unique verification code
    $verificationExpiry = date("Y-m-d H:i:s", strtotime("+1 day")); // Expiry time (1 day from now)

    // Update verification code and expiry in the database
    $updateQuery = "UPDATE login_users SET verification_code = ?, verification_expiry = ? WHERE user_email = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sss", $verificationCode, $verificationExpiry, $userEmail);
    
    if ($updateStmt->execute()) {
        // Send the verification email using PHPMailer
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
            $mail->addAddress($userEmail);

            $mail->Subject = 'Resend Verification - Confirm Your Email';
            $mail->Body    = "Click the link below to verify your email:\n\nhttp://valresidences.dcism.org/pages/verify-email.php?email=" . urlencode($userEmail) . "&code=" . $verificationCode;

            $mail->send();
            echo json_encode(["result" => 1, "message" => "A new verification email has been sent."]);
        } catch (Exception $e) {
            echo json_encode(["result" => -1, "message" => "Failed to send the verification email."]);
            error_log("Failed to send verification email: " . $mail->ErrorInfo);
        }
    } else {
        echo json_encode(["result" => -1, "message" => "Error updating verification code."]);
    }
    
    // Close statements and connection
    $stmt->close();
    $updateStmt->close();
    $conn->close();
}
