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
    <link rel="stylesheet" href="../assets/css/contact-us.css">

    <!-- JavaScript Files (Relative Paths + defer for Performance) -->
    <script src="../assets/js/validate-contact-message.js" defer></script>

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
            <span class="logo-font">Contact Us</span>
            <p>Have questions or need more details about our apartments? Contact us today!</p>
            <button class="scroll-button" data-target="#main-content">GET IN TOUCH</button>
        </div>
    </div>

    <div class="main-content" id="main-content">
        <div class="contact-container" id="contact-container">
            <div class="contact-box">
                <div class="contact-information">
                    <div class="contact-information-content">
                        <img src="../assets/icons/address_contacts_icon.svg" alt="address_contacts_icon">
                        <h3>ADDRESS</h3>
                        <p>Corona del Mar, Pooc, Talisay City, Cebu, Philippines</p>
                    </div>
                    <div class="contact-information-content">
                        <img src="../assets/icons/phone_contacts_icon.svg" alt="phone_contacts_icon">
                        <h3>PHONE</h3>
                        <p>+639999732452 +639186936086</p>
                    </div>
                    <div class="contact-information-content">
                        <img src="../assets/icons/email_contacts_icon.svg" alt="email_contacts_icon">
                        <h3>EMAIL</h3>
                        <p>valresidences@gmail.com</p>
                    </div>
                </div>
                <div class="send-message">
                    <form id="messageForm">
                        <h3>SEND US A MESSAGE</h3>
                        <label for="messageName" id="messageNameLabel">Name</label>
                        <input type="text" class="plus-jakarta-sans" placeholder="Enter your name" id="messageName"
                            required />

                        <label for="messageEmail" id="messageEmailLabel">Email</label>
                        <input type="email" class="plus-jakarta-sans" placeholder="Enter your email" id="messageEmail"
                            required />

                        <label for="messageBody" id="messageBodyLabel">Message</label>
                        <textarea class="plus-jakarta-sans" placeholder="Enter your message" id="messageBody"
                            required></textarea>
                        <button class="plus-jakarta-sans" type="submit" id="submit-message">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    include_once("included-files/footer.html");
    ?>
</body>

</html>