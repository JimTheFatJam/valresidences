<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Val Residences Admin</title>

    <!-- Global Files -->
    <?php
    include_once("included-files/global-user.html");
    ?>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-announcement-popup.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/admin/admin-message-functions.js" defer></script>
    <script src="../assets/js/admin/admin-manage-listings.js" defer></script>
</head>

<body>

    <?php
    require_once "../backend/db_connect.php";
    session_start();
    if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
    $firstName = $_SESSION['first_name'];
    $lastName = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $userStatus = $_SESSION['status'];

    $sqlTotal = "SELECT COUNT(*) AS total FROM apartment_units";
    $resultTotal = $conn->query($sqlTotal);

    $sqlOccupied = "SELECT COUNT(*) AS occupied FROM apartment_units WHERE availability_status = 'occupied'";
    $resultOccupied = $conn->query($sqlOccupied);

    $sqlReserved = "SELECT COUNT(*) AS reserved FROM apartment_units WHERE availability_status = 'reserved'";
    $resultReserved = $conn->query($sqlReserved);

    if ($resultTotal) {
        $rowTotal = $resultTotal->fetch_assoc();
        $totalUnits = $rowTotal['total'];
    } else {
        $totalUnits = 0;
    }

    if ($resultOccupied) {
        $rowOccupied = $resultOccupied->fetch_assoc();
        $unitsOccupied = $rowOccupied['occupied'];
    } else {
        $unitsOccupied = 0;
    }

    if ($resultReserved) {
        $rowReserved = $resultReserved->fetch_assoc();
        $unitsReserved = $rowReserved['reserved'];
    } else {
        $unitsReserved = 0;
    }

    $conn->close();
    ?>

    <!-- Dark Overlay -->
    <div id="popupOverlay" class="overlay"></div>

    <!-- Tenant Announcements Popup -->
    <div class="tenant-announcement-popup" id="tenantAnnouncementPopup"></div>

    <!-- Vacancy Announcement Popup -->
    <div id="vacancyAnnouncementPopup" class="vacancy-announcement-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeVacancyAnnouncementPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">VACANCY ALERT</h2>
        <div class="vacancyAnnouncementForm">
            <label for="vacancySubject" id="vacancySubjectLabel">Subject</label>
            <input type="text" id="vacancySubject" placeholder="Enter subject" required>

            <label for="vacancyMessage" id="vacancyMessageLabel">Message</label>
            <textarea id="vacancyMessage" placeholder="Enter message" required></textarea>

            <div class="vacancy-button-container">
                <button class="plus-jakarta-sans" id="submitSubscriberAnnouncement" onclick="sendVacancyAnnouncement()">SUBMIT</button>
            </div>
        </div>
    </div>


    <div class="top-bar" id="top-bar"></div>

    <div class="content-container">
        <div class="left-content-container">
            <div class="user-navigation-logo">
                <button onclick="location.href='../pages/index.php'">
                    <span class="logo-font">Val</span>
                    <span class="logo-font"><br>Residences</span>
                </button>
            </div>

            <!-- Admin Functions Navigation -->
            <div class="user-navigation">
                <button class="scroll-button" data-target="#top-bar">Dashboard</button>
                <button class="scroll-button" data-target="#management-section-1">Manage Listings</button>
                <button class="scroll-button" data-target="#management-section-2">Manage Tenants</button>
                <button class="scroll-button" data-target="#management-section-3">Send Announcements</button>
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
                    <div class="dashboard-container">
                        <div class="unitsOccupancyStatus">
                            <?php
                            $availableUnits = $totalUnits - $unitsOccupied - $unitsReserved;
                            $availablePercentage = ($totalUnits > 0) ? ($availableUnits / $totalUnits) * 100 : 0;
                            $occupiedPercentage = ($totalUnits > 0) ? ($unitsOccupied / $totalUnits) * 100 : 0;
                            $reservedPercentage = ($totalUnits > 0) ? ($unitsReserved / $totalUnits) * 100 : 0;
                            ?>
                            <h3>UNITS</h3>

                            <div class="circle-status" style="background: conic-gradient(
                            #8ED973 0% <?= $availablePercentage; ?>%, 
                            #FF5050 <?= $availablePercentage; ?>% <?= $availablePercentage + $occupiedPercentage; ?>%, 
                            #FFCC66 <?= $availablePercentage + $occupiedPercentage; ?>% 100%
                            );">
                                <div class="inner-circle">
                                    <h2><?= htmlspecialchars(string: $availableUnits); ?></h2>
                                    <p>Available Units</p>
                                </div>
                            </div>


                            <div class="bottom-text">
                                <p><?= htmlspecialchars($totalUnits); ?> Total units</p>
                                <p><?= htmlspecialchars($unitsOccupied); ?> Occupied</p>
                                <p><?= htmlspecialchars($unitsReserved); ?> Reserved</p>
                            </div>
                        </div>

                        <div class="right-dashboard">
                            <div class="tenantApplicationCount"></div>
                            <div class="maintenanceRequestCount"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-content" id="main-content">
                <div class="management-section" id="management-section-1">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">MANAGE LISTINGS</h5>
                    </div>
                    <div class="manage-listings" id="manage-listings">
                        
                    </div>
                </div>

                <div class="management-section" id="management-section-2">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">MANAGE TENANTS</h5>
                    </div>
                    <div class="manage-tenants">

                    </div>
                </div>

                <div class="management-section" id="management-section-3">
                    <div class="section-header">
                        <h5 class="plus-jakarta-sans">SEND ANNOUNCEMENTS</h5>
                    </div>
                    <div class="send-announcements">
                        <button class="plus-jakarta-sans" onclick="openTenantAnnouncementPopup()">Tenant Announcement</button>
                        <button class="plus-jakarta-sans" onclick="openVacancyAnnouncementPopup()">Vacancy Alert</button>
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