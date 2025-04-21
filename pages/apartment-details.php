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

<body>
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
            <input type="email" id="unitInquiryEmail" placeholder="Enter email" required>

            <label for="unitInquiryUnitMessage" id="unitInquiryUnitMessageLabel">Inquiry</label>
            <textarea id="unitInquiryUnitMessage" placeholder="Enter message" required></textarea>

            <div class="unit-inquiry-button-container">
                <button class="plus-jakarta-sans" id="submitUnitInquiry">SUBMIT</button>
            </div>
        </div>
    </div>

    <?php
    include_once("included-files/header.php");
    ?>

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