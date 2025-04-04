<div class="top-bar"></div>

    <div class="navigation-bar">
        <div class="nav-logo">
            <button onclick="location.href='index.php'">
                <span class="logo-font">Val</span>
                <span class="logo-font"><br>Residences</span>
            </button>
        </div>
        <div class="nav-links">

            <?php
            include_once("included-files/login-signup.html");
            ?>

            <button class="plus-jakarta-sans" onclick="openLoginPopup()">LOGIN</button>
            <button class="plus-jakarta-sans" onclick="openSignUpPopup()">SIGN UP</button>
            <button class="plus-jakarta-sans" onclick="location.href='index.php'">HOME</button>
            <button class="plus-jakarta-sans" onclick="location.href='about.php'">ABOUT</button>
            <button class="plus-jakarta-sans" onclick="location.href='contact.php'">CONTACT</button>
        </div>
    </div>