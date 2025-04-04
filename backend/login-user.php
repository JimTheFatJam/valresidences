<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['loginEmail']) ? trim($_POST['loginEmail']) : "";
    $password = isset($_POST['loginPassword']) ? trim($_POST['loginPassword']) : "";

    // Prepare statement to fetch user_status and password
    $stmt = $conn->prepare("SELECT first_name, last_name, user_status, hashed_password, verification_status FROM login_users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); // Fetch associative array

    if ($row = $result->fetch_assoc()) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $user_status = $row['user_status'];
        $hashed_password = $row['hashed_password'];
        $verification_status = $row['verification_status'];

        if (password_verify($password, $hashed_password)) {
            if ($verification_status) {
                // Store session variables
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['email'] = $email;
                $_SESSION['status'] = $user_status;

                // Redirect based on user role
                echo json_encode([
                    "result" => 1,
                    "message" => "Login successful",
                    "redirect_url" => "../pages/" . $_SESSION['status'] . ".php"
                ]);
                $stmt->close();
                $conn->close();
                exit();
            } else {
                echo json_encode(["result" => -1, "message" => "Your email has not been verified yet. Please check your inbox for the verification link."]);
                $stmt->close();
                $conn->close();
                exit();
            }
        } else {
            echo json_encode(["result" => -1, "message" => "Incorrect credentials"]);
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        echo json_encode(["result" => -1, "message" => "Incorrect credentials"]);
        $stmt->close();
        $conn->close();
        exit();
    }
}
?>