<?php
session_start();

// Optional: Restrict access to only logged-in users
if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];
$email = $_SESSION['email'];
$userStatus = $_SESSION['status'];
?>

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

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/apartment-details.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/apartment-details.js" defer></script>

</head>

<body data-user-status="user" data-user-email="<?= htmlspecialchars($email); ?>">
    <!-- Dark Overlay -->
    <div id="popupOverlay" class="overlay"></div>

    <!-- Inquire Unit -->
    <div id="unitInquiryPopup" class="unit-inquiry-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeInquireUnitPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">UNIT INQUIRY</h2>
        <div class="unitInquiryForm">
            <label for="unitInquiryUnit" id="unitInquiryUnitLabel">Unit</label>
            <input type="text" id="unitInquiryUnit" required disabled>

            <label for="unitInquiryEmail" id="unitInquiryEmailLabel">Email</label>
            <input type="email" id="unitInquiryEmail" placeholder="Enter email" required disabled>

            <label for="unitInquiryUnitMessage" id="unitInquiryUnitMessageLabel">Inquiry</label>
            <textarea id="unitInquiryUnitMessage" placeholder="Enter message" required></textarea>

            <div class="unit-inquiry-button-container">
                <button class="plus-jakarta-sans" id="submitUnitInquiry">SUBMIT</button>
            </div>
        </div>
    </div>

    <!-- Apply Unit -->
    <div id="unitApplyPopup" class="unit-apply-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeApplyUnitPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">UNIT APPLICATION</h2>
        <div class="unitApplyForm">
            <label for="unitApplyUnit" id="unitApplyUnitLabel">Unit</label>
            <input type="text" id="unitApplyUnit" required disabled>

            <label for="unitApplyEmail" id="unitApplyEmailLabel">Email</label>
            <input type="email" id="unitApplyEmail" placeholder="Enter email" required disabled>

            <div class="unit-apply-button-container">
                <button class="plus-jakarta-sans" id="submitUnitApply">SUBMIT</button>
            </div>
        </div>
    </div>

    <div class="top-bar"></div>

    <div class="navigation-bar">
        <div class="nav-logo">
            <button onclick="location.href='../pages/user-view-listings.php'">
                <span class="logo-font">Val</span>
                <span class="logo-font"><br>Residences</span>
            </button>
        </div>
        <div class="nav-links">
            <button class="plus-jakarta-sans" onclick="location.href='../pages/user-view-listings.php'">VIEW
                LISTINGS</button>
            <button class="plus-jakarta-sans" onclick="location.href='../pages/user.php'">USER DASHBOARD</button>
            <button class="plus-jakarta-sans" onclick="location.href='../backend/logout.php'">LOGOUT</button>
        </div>
    </div>

    <div class="landing-section">
        <?php
        include_once("included-files/landing-section.html");
        ?>
        <div class="landing-content">
            <?php
            require_once "../backend/db_connect.php";

            $result = null; // Set default value
            
            if (isset($_GET["apartment_id"])) {
                $apartmentID = $_GET["apartment_id"];

                $query = "SELECT apartment_id, address, subdivision_address, apartment_type, total_units, 
                     units_occupied, units_vacant, map_address, date_listed 
              FROM apartment_listings 
              WHERE apartment_id = ?";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $apartmentID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $apartment = $result->fetch_assoc(); // Fetch data as an associative array
                    echo '<span class="logo-font">' . htmlspecialchars($apartment["subdivision_address"]) . '</span>';
                    echo '<p>' . htmlspecialchars($apartment["address"]) . '<br>';
                    echo htmlspecialchars($apartment["apartment_type"]) . '<br>';
                    echo '(Price range here)</p>';
                    echo '<button class="scroll-button" data-target="#main-content">VIEW UNITS</button>';
                } else {
                    echo '<span class="logo-font">Apartment not found.</span>';
                }

                $stmt->close();
            } else {
                echo '<span class="logo-font">Invalid request.</span>';
            }
            $conn->close();
            ?>
        </div>
    </div>

    <?php
    if ($result && $result->num_rows > 0) {
        echo '<div class="main-content" id="main-content">
                <div class="apartment-units-container" id="apartment-units-container">
                </div>
            </div>';
    }
    ?>

    <?php
    include_once("included-files/footer.html");
    ?>
</body>

</html>