<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
ob_clean();

require_once "db_connect.php";

// Get the POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Initialize response array
$response = array();

// Basic validation
if (empty($email) || empty($new_password) || empty($confirm_password)) {
    $response['result'] = -1;
    $response['message'] = 'All fields are required.';
    echo json_encode($response);
    exit();
}

// Check if the new password and confirm password match
if ($new_password !== $confirm_password) {
    $response['result'] = -1;
    $response['message'] = 'Passwords do not match.';
    echo json_encode($response);
    exit();
}

// Check if the password meets the requirements (optional)
// You can add your own password validation logic here, e.g., checking length, special characters, etc.

// Sanitize email to prevent SQL injection
$email = mysqli_real_escape_string($conn, $email);

// Check if the email exists in the database
$query = "SELECT * FROM login_users WHERE user_email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $response['result'] = -1;
    $response['message'] = 'Email not found.';
    echo json_encode($response);
    exit();
}

// Hash the new password (use password_hash for security)
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the password in the database
$update_query = "UPDATE login_users SET hashed_password = '$hashed_password' WHERE user_email = '$email'";

if (mysqli_query($conn, $update_query)) {
    // If the password was successfully updated
    $response['result'] = 1;
    $response['message'] = 'Password successfully reset.';
} else {
    // If there was an error updating the password
    $response['result'] = -1;
    $response['message'] = 'An error occurred while resetting the password. Please try again.';
}

// Close the database connection
mysqli_close($conn);

// Return the response as JSON
echo json_encode($response);
?>