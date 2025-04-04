document.getElementById("submit-login").addEventListener("click", function (event) {
    event.preventDefault();

    let fields = [
        { id: "loginEmail", label: "loginEmailLabel" },
        { id: "loginPassword", label: "loginPasswordLabel" },
    ];

    let loginEmail = document.getElementById("loginEmail").value.trim();
    let loginPassword = document.getElementById("loginPassword").value.trim();

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
        url: "../backend/login-user.php",
        data: {
            loginEmail: loginEmail,  // No need for quotes around keys
            loginPassword: loginPassword
        },
        dataType: "json"
    }).done(function (data) {
        if (data.result === -1) {
            alert("Error: " + data.message); // Show error message
        } else {
            window.location.href = data.redirect_url; // Redirect if login is successful
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown);
        alert("An error occurred. Please try again.");
    });
});