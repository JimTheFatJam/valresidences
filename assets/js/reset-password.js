isLoading = false;

document.getElementById("submit-reset-password").addEventListener("click", function (event) {
    event.preventDefault();

    let fields = [
        { id: "email", label: "emailLabel" },
        { id: "new_password", label: "newPasswordLabel" },
        { id: "confirm_password", label: "confirmPasswordLabel" }
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submit-reset-password");
    let inputs = document.querySelectorAll("#resetForm input");

    // Remove existing error messages
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate each required field
    fields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "") {
            $("#" + field.label).append('<span class="error-message"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isFieldEmpty = true;
        }
    });

    if (isFieldEmpty) return;

    const requirements = document.querySelectorAll(".reset-password-requirements p");
    const passwordChecks = [
        { element: requirements[0], check: /.{8,}/ },             // 8 characters
        { element: requirements[1], check: /[a-z]/ },              // lowercase
        { element: requirements[2], check: /[A-Z]/ },              // uppercase
        { element: requirements[3], check: /[0-9!@#$%^&*(),.?":{}|<>]/ }  // number or symbol
    ];

    let passwordValid = true;
    
    passwordChecks.forEach(check => {
        if (!check.check.test(document.getElementById("new_password").value)) {
            check.element.classList.remove("valid");
            passwordValid = false;
        } else {
            check.element.classList.add("valid");
        }
    });

    if (!passwordValid) {
        alert("Password too weak! Ensure it meets all the listed requirements.");
        return;
    }

    let email = document.getElementById("email").value.trim();
    let newPassword = document.getElementById("new_password").value.trim();
    let confirmPassword = document.getElementById("confirm_password").value.trim();

    if (newPassword !== confirmPassword) {
        alert("Passwords do not match.");
        return;
    }

    $.ajax({
        method: "POST",
        url: "../backend/reset-password.php",
        data: {
            email: email,
            new_password: newPassword,
            confirm_password: confirmPassword
        },
        dataType: "json",
        beforeSend: function () {
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;  // Show spinner
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        if (data.result === -1) {
            alert("Error: " + data.message);
            isLoading = false;
            submitButton.innerHTML = "Reset Password";
            submitButton.disabled = false;
            inputs.forEach(input => input.disabled = false);
        } else {
            alert(data.message);
            window.location.href = "../pages/index.php"; // Redirect after successful reset
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown);
        alert("An error occurred. Please try again.");
        isLoading = false;
        submitButton.innerHTML = "Reset Password";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
});

// Event listener for updating password requirements dynamically
document.getElementById("new_password").addEventListener("input", function () {
    const requirements = document.querySelectorAll(".reset-password-requirements p");
    const passwordChecks = [
        { element: requirements[0], check: /.{8,}/ },             // 8 characters
        { element: requirements[1], check: /[a-z]/ },              // lowercase
        { element: requirements[2], check: /[A-Z]/ },              // uppercase
        { element: requirements[3], check: /[0-9!@#$%^&*(),.?":{}|<>]/ }  // number or symbol
    ];

    let isValid = true;

    passwordChecks.forEach(check => {
        const passwordField = document.getElementById("new_password");
        if (!check.check.test(passwordField.value)) {
            check.element.classList.remove("valid");
            passwordField.style.borderLeft = "2px solid red"; // Add a red border if invalid
            check.element.style.color = "red"; // Change text color to red
            isValid = false;
        } else {
            check.element.classList.add("valid");
            passwordField.style.borderLeft = "2px solid green"; // Add a green border if valid
            check.element.style.color = "green"; // Change text color to green
        }
    });

    // Optionally, you can change the style of the requirements container based on validity
    const requirementsContainer = document.querySelector(".reset-password-requirements");
    if (isValid) {
        requirementsContainer.style.borderLeft = "2px solid green"; // Green border if valid
    } else {
        requirementsContainer.style.borderLeft = "2px solid red"; // Red border if invalid
    }
});