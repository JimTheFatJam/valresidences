<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Val Residences</title>

    <!-- Global Files -->
    <?php
    include_once("included-files/global.html");
    ?>

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/verify-again.js" defer></script>
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

            <!-- Dark Overlay for Login Popup -->
            <div id="popupOverlay" class="overlay"></div>

            <!-- Login Popup -->
            <div id="loginPopup" class="login-popup">
                <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeLoginPopup()"
                    alt="close_button">
                <h2 class="plus-jakarta-sans-bold">LOGIN</h2>
                <form>
                    <label for="loginEmail" id="loginEmailLabel">Email</label>
                    <input type="text" id="loginEmail" placeholder="Enter your email" required>

                    <label for="loginPassword" id="loginPasswordLabel">Password</label>
                    <div class="login-password-container">
                        <input type="password" id="loginPassword" placeholder="Enter your password" required>
                        <img src="../assets/icons/eye_icon_crossed.svg" id="toggleLoginPassword" alt="Toggle Password">
                    </div>

                    <a href="#" class="forgot-password">Forgot password?</a>

                    <button class="plus-jakarta-sans" type="submit" id="submit-login">LOGIN</button>

                    <p class="signup-text">Not registered? <a href="#"
                            onclick="closeLoginPopup(); openSignUpPopup(); return false;">Sign up now</a></p>
                </form>
            </div>

            <!-- Sign Up Popup -->
            <div id="signUpPopup" class="sign-up-popup">
                <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeSignUpPopup()"
                    alt="close_button">
                <h2 class="plus-jakarta-sans-bold">SIGN UP</h2>
                <form id="registerForm">
                    <div class="signup-body">
                        <div class="signup-left">
                            <label for="firstName" id="firstNameLabel">First Name</label>
                            <input type="text" id="firstName" placeholder="Enter your first name" required>

                            <label for="lastName" id="lastNameLabel">Last Name</label>
                            <input type="text" id="lastName" placeholder="Enter your last name" required>

                            <label for="registerEmail" id="registerEmailLabel">Email</label>
                            <input type="email" id="registerEmail" placeholder="Enter your email" required>
                        </div>

                        <div class="signup-right">
                            <label for="signUpPassword" id="signUpPasswordLabel">Password</label>
                            <div class="sign-up-password-container">
                                <input type="password" id="signUpPassword" placeholder="Enter your password" required>
                                <img src="../assets/icons/eye_icon_crossed.svg" id="toggleSignUpPassword"
                                    alt="Toggle Password">
                            </div>

                            <div class="signup-password-requirements">
                                <p>At least 8 characters</p>
                                <p>At least one small letter</p>
                                <p>At least one capital letter</p>
                                <p>At least one number or symbol</p>
                            </div>

                            <label for="confirmPassword" id="confirmPasswordLabel">Confirm Password</label>
                            <input type="password" id="confirmPassword" placeholder="Confirm your password" required>
                        </div>
                    </div>

                    <div class="signup-button-container">
                        <button class="plus-jakarta-sans" type="submit" id="submit-register">SUBMIT</button>
                    </div>
                </form>
            </div>

            <button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>
            <button class="plus-jakarta-sans" onclick="openSignUpPopup()">SIGN UP</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/index.php'">HOME</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/about.php'">ABOUT</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/contact.php'">CONTACT</button>
        </div>
    </div>

    <div class="landing-section">
        <?php
        include_once("included-files/landing-section.html");
        ?>
        <div class="landing-content">
            <?php
            require_once "../backend/db_connect.php";

            if (isset($_GET["email"]) && isset($_GET["code"])) {
                $email = urldecode($_GET["email"]);
                $code = $_GET["code"];

                // Prepare query to check if email and code exist
                $stmt = $conn->prepare("SELECT user_id, verification_expiry, verification_status FROM login_users WHERE user_email = ? AND verification_code = ?");
                $stmt->bind_param("ss", $email, $code);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($user_id, $verification_expiry, $verification_status);
                    $stmt->fetch();

                    // Check if account is already verified
                    if ($verification_status == 1) {
                        echo '<span class="logo-font">Your account has already been verified.</span>';
                        echo '<button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>';
                    }
                    // Check if the verification code is still valid
                    else if (strtotime($verification_expiry) > time()) {
                        // Update verification status
                        $updateStmt = $conn->prepare("UPDATE login_users SET verification_status = 1, verified_at = NOW() WHERE user_id = ?");
                        $updateStmt->bind_param("i", $user_id);

                        if ($updateStmt->execute()) {
                            echo '<span class="logo-font">Email verification successful! You can now log in.</span>';
                            echo '<button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>';
                        } else {
                            echo '<span class="logo-font">Verification failed. Please try again later.</span>';
                        }

                        $updateStmt->close();
                    } else {
                        echo '<span class="logo-font">Your verification link has expired. Please request a new one.</span>';
                        echo '<button class="plus-jakarta-sans" id="verify-again">VERIFY AGAIN</button>';
                    }
                } else {
                    echo '<span class="logo-font">Invalid verification link.</span>';
                }

                $stmt->close();
                $conn->close();
            } else {
                echo '<span class="logo-font">Invalid request.</span>';
            }
            ?>
        </div>
    </div>

    <div class="main-content">
    </div>

    <?php
    include_once("included-files/footer.html");
    ?>
</body>

</html>