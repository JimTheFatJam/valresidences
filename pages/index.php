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
    <link rel="stylesheet" href="../assets/css/apartment-listings.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/apartment-listings.js" defer></script>

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
            <span class="logo-font">Val Residences</span>
            <p>Wake up to refreshing mornings in our apartments at Corona del Mar, a Spanish Mediterranean-inspired
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