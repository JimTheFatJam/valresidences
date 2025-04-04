let isLoading = false;

document.getElementById("submit-register").addEventListener("click", function (event) {
    event.preventDefault();

    let fields = [
        { id: "firstName", label: "firstNameLabel" },
        { id: "lastName", label: "lastNameLabel" },
        { id: "registerEmail", label: "registerEmailLabel" },
        { id: "signUpPassword", label: "signUpPasswordLabel" },
        { id: "confirmPassword", label: "confirmPasswordLabel" }
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submit-register");
    let inputs = document.querySelectorAll("#registerForm input"); // Select all inputs

    // Remove existing error indicators
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate fields dynamically
    fields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "") {
            $("#" + field.label).append('<span class="error-message"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isFieldEmpty = true;
        }
    });

    if (isFieldEmpty) return;

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let registerEmail = document.getElementById("registerEmail").value.trim();

    if (!emailRegex.test(registerEmail)) {
        alert("Please enter a valid email address with a correct domain (e.g., example@mail.com)");
        return;
    }

    const requirements = document.querySelectorAll(".signup-password-requirements p");
    const passwordChecks = [
        { element: requirements[0] }, // At least 8 characters
        { element: requirements[1] }, // At least one lowercase letter
        { element: requirements[2] }, // At least one uppercase letter
        { element: requirements[3] }  // At least one number or symbol
    ];

    if (!passwordChecks.every(check => check.element.classList.contains("valid"))) {
        alert("Password too weak! Ensure it meets all the listed requirements.");
        return;
    }

    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let signUpPassword = document.getElementById("signUpPassword").value.trim();
    let confirmPassword = document.getElementById("confirmPassword").value.trim();

    if (signUpPassword != confirmPassword) {
        alert("Passwords do not match. Please try again.");
        return;
    }

    $.ajax({
        method: "POST",
        url: "../backend/register-user.php",
        data: {
            firstName: firstName,
            lastName: lastName,
            registerEmail: registerEmail,
            signUpPassword: signUpPassword,
            confirmPassword: confirmPassword
        },
        dataType: "json",
        beforeSend: function () {
            // **Enable Loading State ONLY if request starts**
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        if (data.result === -1) {
            alert("Error: " + data.message);
            // **Reset UI when email exists or error occurs**
            isLoading = false;
            submitButton.innerHTML = "Submit";
            submitButton.disabled = false;
            inputs.forEach(input => input.disabled = false);
        } else {
            alert(data.message);
            location.reload();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown);
        alert("An error occurred. Please try again.");
        // **Reset UI on request failure**
        isLoading = false;
        submitButton.innerHTML = "Submit";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
});