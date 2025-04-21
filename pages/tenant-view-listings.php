<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Val Residences Tenant</title>

    <!-- Global Files -->
    <?php
    include_once("included-files/global.html");
    ?>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/apartment-listings.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/apartment-listings-user.js" defer></script>

</head>

<body>
    <?php
    session_start();

    // Optional: Restrict access to only logged-in users
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

    <div class="navigation-bar">
        <div class="nav-logo">
            <button onclick="location.href='../pages/tenant-view-listings.php'">
                <span class="logo-font">Val</span>
                <span class="logo-font"><br>Residences</span>
            </button>
        </div>
        <div class="nav-links">
            <button class="plus-jakarta-sans" onclick="location.href='../pages/tenant-view-listings.php'">VIEW
                LISTINGS</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/tenant.php'">TENANT DASHBOARD</button>
            <button class="plus-jakarta-sans" onclick="location.href='../backend/logout.php'">LOGOUT</button>
        </div>
    </div>

    <div class="landing-section">
        <?php
        include_once("included-files/landing-section.html");
        ?>
        <div class="landing-content">
            <span class="logo-font">Welcome, <?= htmlspecialchars($firstName); ?></span>
            <p style="font-size:1.5rem;">Signed in as <?= htmlspecialchars($userStatus); ?></p>
            <p style="font-size:1.1rem;">Wake up to refreshing mornings in our apartments at Corona del Mar, a Spanish Mediterranean-inspired
                seaside community in Cebu.</p>
            <button class="scroll-button" data-target="#main-content">VIEW LISTINGS</button>
        </div>
    </div>

    <div class="main-content" id="main-content">
        <div class="apartment-listings-container" id="apartment-listings">
        </div>
    </div>

    <?php
    include_once("included-files/footer.html");
    ?>

</body>

</html>