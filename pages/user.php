<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Val Residences User</title>

    <!-- Global Files -->
    <?php
    include_once("included-files/global-user.html");
    ?>

    <!-- CSS Files -->
    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
</head>

<body>

    <?php
    session_start();
    if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'user') {
        header("Location: ../index.php");
        exit();
    }
    $firstName = $_SESSION['first_name'];
    $lastName = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $userStatus = $_SESSION['status'];
    ?>

    <div class="top-bar"></div>

    <div class="content-container">
        <div class="left-content-container">
            <div class="user-navigation-logo">
                <button onclick="location.href='../pages/index.php'">
                    <span class="logo-font">Val</span>
                    <span class="logo-font"><br>Residences</span>
                </button>
            </div>

            <!-- User Functions -->
            <div class="user-navigation">
                <button class="scroll-button" data-target="#application-status">Application Status</button>
                <button class="scroll-button" data-target="#contact-landlord">Contact Landlord</button>
                <button class="scroll-button" data-target="#request-maintenance">Request Maintenance</button>
            </div>
        </div>

        <div class="right-content-container">
            <div class="navigation-bar">
                <div class="nav-links">
                    <button class="plus-jakarta-sans" onclick="location.href=''">MANAGE ACCOUNT</button>
                    <button class="plus-jakarta-sans" onclick="location.href='../backend/logout.php'">LOGOUT</button>
                </div>
            </div>

            <div class="landing-section">
                <?php
                include_once("included-files/landing-section.html");
                ?>
                <div class="landing-content">
                    <span class="logo-font">Welcome, <?= htmlspecialchars($firstName); ?></span>
                    <p>Signed in as <?= htmlspecialchars($userStatus); ?></p>
                    <!-- Dashboard view -->
                </div>
            </div>

            <div class="main-content" id="main-content">
                <div class="management-section" id="application-status">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">APPLICATION STATUS</h5>
                    </div>
                    <div class="section-body">

                    </div>
                </div>

                <div class="management-section" id="contact-landlord">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">CONTACT LANDLORD</h5>
                    </div>
                    <div class="section-body">
                        
                    </div>
                </div>

                <div class="management-section" id="request-maintenance">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">REQUEST MAINTENANCE</h5>
                    </div>
                    <div class="section-body">
                        
                    </div>
                </div>
            </div>

            <?php
            include_once("included-files/footer.html");
            ?>
        </div>
    </div>

</body>

</html>