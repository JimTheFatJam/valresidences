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
    <link rel="stylesheet" href="../assets/css/about-us.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/enlarge-image.js" defer></script>

</head>

<body>
    <?php
    include_once("included-files/header.php");
    ?>

    <div class="landing-section">
        <?php
        include_once("included-files/landing-section.html");
        ?>
        <div class="landing-content">
            <span class="logo-font">About Us</span>
            <p>Enjoy stunning views and a revitalizing sea breeze for a truly relaxing stay here at Val Residences at
                Corona del Mar.</p>
            <button class="scroll-button" data-target="#about-us-main-content">READ MORE</button>
        </div>
    </div>

    <div class="about-us-main-content" id="about-us-main-content">
        <div class="about-us-container" id="about-us-container">
            <div class="text-column">
                <h2>Welcome to Val Residences</h2>
                <p>Your home in the exclusive seaside community of Corona del Mar in Talisay City, Cebu. Nestled within
                    this Spanish Mediterranean-inspired beachfront subdivision, our apartments offer the perfect blend
                    of resort-style living and modern city convenience.</p>

                <p>Just a 15-minute drive from Cebu City via the South Coastal Road, Corona del Mar is the first
                    residential beachfront development of its kind in Cebu. Here, you'll wake up to breathtaking
                    panoramic views, a refreshing sea breeze, and the serenity of living close to nature—while still
                    enjoying easy access to schools, businesses, and shopping centers.</p>

                <h3>Features & Amenities</h3>
                <ul>
                    <li>Beach Frontage facing Bohol Strait</li>
                    <li>Well-lighted Spine Road with Tree Lines</li>
                    <li>Clubhouse</li>
                    <li>Infinity Pool with Shower Rooms</li>
                    <li>View Tower</li>
                    <li>Gazebo</li>
                    <li>Tennis and Basketball Courts</li>
                    <li>Landscaped Open Area</li>
                    <li>Pocket Parks</li>
                </ul>
            </div>
            <div class="images-column">
                <div class="image-grid">
                    <img src="../assets/images/about_us_images/image1.jpg" alt="Image 1" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image2.jpg" alt="Image 2" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image3.jpg" alt="Image 3" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image4.jpg" alt="Image 4" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image5.jpg" alt="Image 5" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image6.jpg" alt="Image 6" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image7.jpg" alt="Image 7" onclick="openModal(this)">
                    <img src="../assets/images/about_us_images/image8.jpg" alt="Image 8" onclick="openModal(this)">
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="overlay" onclick="closeModal()">
        <img id="modalImage" class="enlarged-img" />
    </div>

    <?php
    include_once("included-files/footer.html");
    ?>
    
</body>

</html>