document.addEventListener("DOMContentLoaded", function () {
    function setupPasswordToggle(passwordInputId, toggleButtonId) {
        let passwordInput = document.getElementById(passwordInputId);
        let toggleButton = document.getElementById(toggleButtonId);

        if (!passwordInput || !toggleButton) return; // Prevent errors if elements don't exist

        // Initially hide the eye icon
        toggleButton.style.display = "none";

        // Show/hide eye icon based on input field
        passwordInput.addEventListener("input", function () {
            if (passwordInput.value.trim().length > 0) {
                toggleButton.style.display = "inline-block";
                toggleButton.src = "../assets/icons/eye_icon_crossed.svg"; // Show crossed eye initially
            } else {
                toggleButton.style.display = "none";
            }
        });

        // Toggle password visibility
        toggleButton.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.src = "../assets/icons/eye_icon.svg"; // Regular eye when visible
            } else {
                passwordInput.type = "password";
                toggleButton.src = "../assets/icons/eye_icon_crossed.svg"; // Crossed eye when hidden
            }
        });
    }

    // Apply function to both login and sign-up password fields
    setupPasswordToggle("loginPassword", "toggleLoginPassword");
    setupPasswordToggle("signUpPassword", "toggleSignUpPassword");
});