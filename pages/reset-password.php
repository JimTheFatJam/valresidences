<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Val Residences</title>

    <?php include_once("included-files/global.html"); ?>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/reset-password.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/reset-password.js" defer></script>
</head>

<body>
    <div class="top-bar"></div>

    <div class="navigation-bar">
        <div class="nav-logo">
            <button onclick="location.href='../pages/index.php'">
                <span class="logo-font">Val</span>
                <span class="logo-font"><br>Residences</span>
            </button>
        </div>
        <div class="nav-links">
            <?php include_once("included-files/login-signup.html"); ?>
            <button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>
            <button class="plus-jakarta-sans" onclick="openSignUpPopup()">SIGN UP</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/index.php'">HOME</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/about.php'">ABOUT</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/contact.php'">CONTACT</button>
        </div>
    </div>

    <div class="landing-section">
        <?php include_once("included-files/landing-section.html"); ?>
        <div class="landing-content">
            <?php
            require_once "../backend/db_connect.php";

            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($email, $expires_at);
                    $stmt->fetch();

                    if (strtotime($expires_at) < time()) {
                        echo '<span class="logo-font">This reset link has expired. Please request a new one.</span>';
                    } else {
                        ?>
                        <div class="reset-popup">
                            <h2>RESET PASSWORD</h2>
                            <form id="resetForm" method="POST">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>

                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required>

                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>

                                <div class="reset-password-requirements">
                                    <p>At least 8 characters</p>
                                    <p>At least one lowercase letter</p>
                                    <p>At least one uppercase letter</p>
                                    <p>At least one number or symbol</p>
                                </div>

                                <button type="submit" id="submit-reset-password">Reset Password</button>
                            </form>
                        </div>
                        <?php
                    }
                } else {
                    echo '<span class="logo-font">Invalid or used reset link.</span>';
                }

                $stmt->close();
            } elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset_password"])) {
                $email = $_POST['email'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                if ($new_password !== $confirm_password) {
                    echo '<span class="logo-font">Passwords do not match.</span>';
                } else {
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password
                    $updateStmt = $conn->prepare("UPDATE login_users SET hashed_password = ? WHERE user_email = ?");
                    $updateStmt->bind_param("ss", $hashedPassword, $email);

                    if ($updateStmt->execute()) {
                        // Delete the used reset token
                        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                        $deleteStmt->bind_param("s", $email);
                        $deleteStmt->execute();

                        echo '<span class="logo-font">Password successfully reset! You can now log in.</span>';
                        echo '<button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>';
                    } else {
                        echo '<span class="logo-font">Failed to reset password. Please try again.</span>';
                    }

                    $updateStmt->close();
                }
            } else {
                echo '<span class="logo-font">Invalid request.</span>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <?php include_once("included-files/footer.html"); ?>
</body>

</html>