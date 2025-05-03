<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Val Residences Tenant</title>

    <!-- Global Files -->
    <?php
    include_once("included-files/global-user.html");
    ?>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/user-tenant.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/user-message-function.js" defer></script>
    <script src="../assets/js/tenant/view-lease-details.js" defer></script>
</head>

<body data-user-status="Tenant">

    <?php
    session_start();
    if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'tenant') {
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
                <button onclick="location.href='../pages/tenant-view-listings.php'">
                    <span class="logo-font">Val</span>
                    <span class="logo-font"><br>Residences</span>
                </button>
            </div>

            <!-- Tenant Functions -->
            <div class="user-navigation">
                <button class="scroll-button" data-target="#lease-details">Lease Details</button>
                <button class="scroll-button" data-target="#contact-landlord">Contact Landlord</button>
            </div>
        </div>

        <div class="right-content-container">
            <div class="navigation-bar">
                <div class="nav-links">
                    <button class="plus-jakarta-sans" onclick="location.href='../pages/tenant-view-listings.php'">VIEW
                        LISTINGS</button>
                    <button class="plus-jakarta-sans" onclick="location.href='../pages/tenant.php'">TENANT
                        DASHBOARD</button>
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
                <div class="management-section" id="lease-details">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">LEASE DETAILS</h5>
                    </div>
                    <div class="lease-details-container">

                    </div>
                </div>

                <div class="management-section" id="contact-landlord">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">CONTANCT LANDLORD</h5>
                    </div>
                    <div class="contact-landlord-container">
                        <div class="contactLandLordForm">
                            <label for="contactEmail" id="contactEmailLabel">Email</label>
                            <input type="text" id="contactEmail" value="<?php echo htmlspecialchars($email); ?>"
                                required disabled>

                            <label for="contactSubject" id="contactSubjectLabel">Subject</label>
                            <input type="text" id="contactSubject" placeholder="Enter subject" required>

                            <label for="contactMessage" id="contactMessageLabel">Message</label>
                            <textarea id="contactMessage" placeholder="Enter message" required></textarea>

                            <div class="contact-landlord-button-container">
                                <button class="plus-jakarta-sans" id="submitContactLandlord"
                                    onclick="sendContactLandlord()">SUBMIT</button>
                            </div>
                        </div>
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