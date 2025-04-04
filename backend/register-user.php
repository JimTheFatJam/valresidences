<?php
require_once "db_connect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : "";
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : "";
    $registerEmail = isset($_POST['registerEmail']) ? trim($_POST['registerEmail']) : "";
    $signUpPassword = isset($_POST['signUpPassword']) ? trim($_POST['signUpPassword']) : "";

    $stmt = $conn->prepare("SELECT user_id FROM login_users WHERE user_email = ?");
    $stmt->bind_param("s", $registerEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["result" => -1, "message" => "Email already exists!"]);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Hash the password
    $hashedPassword = password_hash($signUpPassword, PASSWORD_DEFAULT);

    // Insert new user with email verification fields
    $stmt = $conn->prepare("INSERT INTO login_users 
    (first_name, last_name, user_email, hashed_password, user_status, verification_status, verification_code, verification_expiry, verified_at, created_at) 
    VALUES (?, ?, ?, ?, 'user', 0, ?, ?, NULL, NOW())");

    $verificationCode = bin2hex(random_bytes(16)); // Generate a unique verification code
    $verificationExpiry = date("Y-m-d H:i:s", strtotime("+1 day")); // Expiry time (1 day from now)

    $stmt->bind_param("ssssss", $firstName, $lastName, $registerEmail, $hashedPassword, $verificationCode, $verificationExpiry);

    if ($stmt->execute()) {
        // Send verification email using PHPMailer
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
            $mail->addAddress($registerEmail, $firstName . ' ' . $lastName);

            $mail->Subject = 'Verify Your Email - Val Residences';
            $mail->Body    = "Hello $firstName,\n\nPlease verify your email by clicking the link below:\nhttp://valresidences.dcism.org/pages/verify-email.php?email=" . urlencode($registerEmail) . "&code=" . $verificationCode . "\n\nThis link will expire in 24 hours.";

            $mail->send();
            echo json_encode(["result" => 1, "message" => "Registration successful! Please check your email to verify your account."]);
        } catch (Exception $e) {
            echo json_encode(["result" => -1, "message" => "User registered, but email sending failed. Please contact support."]);
            error_log("Failed to send verification email: " . $mail->ErrorInfo);
        }
    } else {
        echo json_encode(["result" => -1, "message" => "An error occurred during registration. Please try again."]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
