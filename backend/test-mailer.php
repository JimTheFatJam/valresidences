<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust if needed

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplyvalresidences@gmail.com';
    $mail->Password   = 'uiwv ifsm arbk kfek'; // App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('noreplyvalresidences@gmail.com', 'Val Residences Test');
    $mail->addAddress('your_email@gmail.com'); // Replace with your email

    // Content
    $mail->Subject = 'PHPMailer Test from Server';
    $mail->Body    = 'If you receive this, PHPMailer is working on the server!';

    $mail->send();
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Email could not be sent.<br>";
    echo "Mailer Error: " . $mail->ErrorInfo;
}