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
    <link rel="stylesheet" href="../assets/css/admin-manage-listings-popup.css">
    <link rel="stylesheet" href="../assets/css/modal.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/admin/admin-message-functions.js" defer></script>
    <script src="../assets/js/admin/admin-manage-listings.js" defer></script>
    <script src="../assets/js/admin/admin-manage-listings-functions.js" defer></script>
    <script src="../assets/js/admin/admin-image-functions.js" defer></script>
    <script src="../assets/js/admin/admin-manage-units-functions.js" defer></script>
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

    <!-- Add Apartment Popup -->
    <div id="addApartmentPopup" class="add-apartment-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeAddApartmentPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">ADD APARTMENT</h2>
        <div class="addApartmentForm">
            <div class="functions">
                <div class="left-side">
                    <label for="apartmentSubdivisionAddress" id="apartmentSubdivisionAddressLabel">Subdivision Address</label>
                    <input type="text" id="apartmentSubdivisionAddress" placeholder="Enter subdivision address" required>

                    <label for="apartmentAddress" id="apartmentAddressLabel">Address</label>
                    <input type="text" id="apartmentAddress" placeholder="Enter address" required>

                    <label for="apartmentType" id="apartmentTypeLabel">Apartment Type</label>
                    <input type="text" id="apartmentType" placeholder="Enter apartment type" required>

                    <label for="apartmentMapURL" id="apartmentMapURLLabel">Map URL</label>
                    <input type="text" id="apartmentMapURL" placeholder="Enter map URL" required>
                </div>
                <div class="right-side">
                    <label for="apartmentImages" id="apartmentImagesLabel">Apartment Images</label>
                    <input type="file" id="apartmentImages" name="apartmentImages[]" accept="image/*" multiple required>
                    <div class="preview-apartment-images" id="previewApartmentImages">
                    </div>
                </div>
            </div>
            <div class="add-apartment-button-container">
                <button class="plus-jakarta-sans" id="submitNewApartment" onclick="submitNewApartment()">SUBMIT</button>
            </div>
        </div>
     </div>

    <!-- Add Unit Popup -->
     <div id="addUnitPopup" class="add-unit-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeAddUnitPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">ADD UNIT</h2>
        <div class="addUnitForm">
            <div class="functions">
                <div class="left-side">
                    <div class="function-group">
                        <label for="unitNumber" id="unitNumberLabel">Unit</label>
                        <input type="number" id="unitNumber" placeholder="Enter unit number" min="1" step="1" required>
                    
                        <label for="bedroomCount" id="bedroomCountLabel">Bedrooms</label>
                        <input type="number" id="bedroomCount" placeholder="Enter number of bedrooms" min="1" step="1" required>
                    
                        <label for="parkingSpaceBool" id="parkingSpaceBoolLabel">Parking Space</label>
                        <select id="parkingSpaceBool" required>
                            <option value="" disabled selected hidden>Select an option</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                        <label for="rentPrice" id="rentPriceLabel">Rent Price</label>
                        <input type="number" id="rentPrice" placeholder="Enter rent price" min="0" step="0.01" required>
                    
                        <label for="availabilityStatus" id="availabilityStatusLabel">Availability Status</label>
                        <select id="availabilityStatus" required>
                            <option value="" disabled selected hidden>Select an option</option>
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                        </select>
                    </div>

                    <div class="function-group">
                        <label for="floorCount" id="floorCountLabel">Floors</label>
                        <input type="number" id="floorCount" placeholder="Enter number of floors" min="1" step="1" required>

                        <label for="tbCount" id="tbCountLabel">T&B</label>
                        <input type="number" id="tbCount" placeholder="Enter number of toilets and baths" min="1" step="1" required>
                    
                        <label for="petFriendlyBool" id="petFriendlyBoolLabel">Pet Friendly</label>
                        <select id="petFriendlyBool" required>
                            <option value="" disabled selected hidden>Select an option</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                        <label for="monthAdvance" id="monthAdvanceLabel">Month Advance</label>
                        <input type="number" id="monthAdvance" placeholder="Enter number of months" min="1" step="1" required>
                    
                        <label for="furnishingStatus" id="furnishingStatusLabel">Furnishing Status</label>
                        <select id="furnishingStatus" required>
                            <option value="" disabled selected hidden>Select an option</option>
                            <option value="Furnished">Furnished</option>
                            <option value="Semi-furnished">Semi-furnished</option>
                            <option value="Unfurnished">Unfurnished</option>
                        </select>
                    </div>

                    <div class="function-group">
                        <label for="livingArea" id="livingAreaLabel">Living Area (sqm)</label>
                        <input type="number" id="livingArea" placeholder="Enter living area" min="0" step="0.01" required>
                    
                        <label for="balconyBool" id="balconyBoolLabel">Balcony</label>
                        <select id="balconyBool" required>
                            <option value="" disabled selected hidden>Select an option</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                        <label for="leaseTerm" id="leaseTermLabel">Lease Term (yr)</label>
                        <input type="number" id="leaseTerm" placeholder="Enter lease term" min="1" step="1" required>

                        <label for="monthDeposit" id="monthDepositLabel">Month Deposit</label>
                        <input type="number" id="monthDeposit" placeholder="Enter number of months" min="1" step="1" required>
                    </div>
                </div>
                <div class="right-side">
                    <label for="unitImages" id="unitImagesLabel">Unit Images</label>
                    <input type="file" id="unitImages" name="unitImages[]" accept="image/*" multiple required>
                    <div class="preview-unit-images" id="previewUnitImages">
                    </div>
                </div>
            </div>
            <div class="add-unit-button-container">
                <button class="plus-jakarta-sans" id="submitNewUnit">SUBMIT</button>
            </div>
        </div>
     </div>

    <!-- Tenant Announcements Popup -->
    <div id="tenantAnnouncementPopup" class="tenant-announcement-popup">
        <img class="close_button" src="../assets/icons/close_button.svg" onclick="closeTenantAnnouncementPopup()"
            alt="close_button">
        <h2 class="plus-jakarta-sans-bold">TENANT ANNOUNCEMENT</h2>
        <div class="tenantAnnouncementForm">
            <label for="tenantSubject" id="tenantSubjectLabel">Subject</label>
            <input type="text" id="tenantSubject" placeholder="Enter subject" required>

            <label for="tenantMessage" id="tenantMessageLabel">Message</label>
            <textarea id="tenantMessage" placeholder="Enter message" required></textarea>

            <div class="tenant-button-container">
                <button class="plus-jakarta-sans" id="submitTenantAnnouncement" onclick="sendTenantAnnouncement()">SUBMIT</button>
            </div>
        </div>
    </div>

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
                        <div class="manage-apartments" id="manage-apartments"></div>
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