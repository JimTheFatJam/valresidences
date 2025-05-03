document.getElementById("submit-reset").addEventListener("click", function (event) {
    event.preventDefault();

    let fields = [
        { id: "resetEmail", label: "resetEmailLabel" }
    ];

    let inputs = document.querySelectorAll("#forgotPasswordForm input"); // Select all inputs
    let resetEmail = document.getElementById("resetEmail").value.trim();
    let submitButton = document.getElementById("submit-reset");

    let isValid = true;

    // Remove existing error indicators
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate fields dynamically
    fields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "") {
            $("#" + field.label).append('<span class="error-message"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isValid = false;
        }
    });

    if (!isValid) return;

    $.ajax({
        method: "POST",
        url: "../backend/reset-password-email.php",
        data: {
            resetEmail: resetEmail
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
            // Reset UI
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
        // Reset UI on request failure
        isLoading = false;
        submitButton.innerHTML = "Submit";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
});