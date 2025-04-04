document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("signUpPassword"); // Adjust if needed
    const requirements = document.querySelectorAll(".signup-password-requirements p");

    // Define password requirements as objects
    const passwordChecks = [
        { regex: /.{8,}/, element: requirements[0] }, // At least 8 characters
        { regex: /[a-z]/, element: requirements[1] }, // At least one lowercase letter
        { regex: /[A-Z]/, element: requirements[2] }, // At least one uppercase letter
        { regex: /[\d\W]/, element: requirements[3] }  // At least one number or symbol
    ];

    // Function to validate the password
    function validatePassword() {
        const password = passwordInput.value;

        passwordChecks.forEach(check => {
            if (password.length === 0) {
                // Reset to default gray when input is empty
                check.element.classList.remove("valid", "invalid");
            } else if (check.regex.test(password)) {
                // If requirement is met, apply 'valid' class
                check.element.classList.add("valid");
                check.element.classList.remove("invalid");
            } else {
                // If requirement is not met, apply 'invalid' class
                check.element.classList.add("invalid");
                check.element.classList.remove("valid");
            }
        });
    }

    // Listen for input events
    passwordInput.addEventListener("input", validatePassword);
});