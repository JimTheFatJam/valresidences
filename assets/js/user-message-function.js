document.getElementById("submitContactLandlord").addEventListener("click", function (event) {
    event.preventDefault();

    const userStatus = document.body.dataset.userStatus;

    let messageFields = [
        { id: "contactEmail", label: "contactEmailLabel" },
        { id: "contactSubject", label: "contactSubjectLabel" },
        { id: "contactMessage", label: "contactMessageLabel" },
    ];

    let isMessageFieldsEmpty = false;
    let submitMessageButton = document.getElementById("submitContactLandlord");
    let messageInputs = document.querySelectorAll("#contactSubject, #contactMessage");

    // Remove existing error indicators
    $(".error-message").remove();
    messageFields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate message fields dynamically
    messageFields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "") {
            $("#" + field.label).append('<span class="error-message"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isMessageFieldsEmpty = true;
        }
    });

    if (isMessageFieldsEmpty) return;

    let contactEmail = document.getElementById("contactEmail").value.trim();
    let contactSubject = document.getElementById("contactSubject").value.trim();
    let contactMessage = document.getElementById("contactMessage").value.trim();

    $.ajax({
        method: "POST",
        url: "../backend/user-message.php",
        data: {
            userStatus: userStatus,
            contactEmail: contactEmail,
            contactSubject: contactSubject,
            contactMessage: contactMessage,
        },
        dataType: "json",
        beforeSend: function () {
            // **Enable Loading State ONLY if request starts**
            isLoading = true;
            submitMessageButton.innerHTML = `<div class="spinner"></div>`;
            submitMessageButton.disabled = true;
            messageInputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        if (data.result === -1) {
            alert("Error: " + data.message);
            // **Reset UI when email exists or error occurs**
            isLoading = false;
            submitMessageButton.innerHTML = "Submit";
            submitMessageButton.disabled = false;
            messageInputs.forEach(input => input.disabled = false);
        } else {
            alert(data.message);
            location.reload();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
        alert("Server Response: " + jqXHR.responseText);
        // **Reset UI on request failure**
        isLoading = false;
        submitMessageButton.innerHTML = "Submit";
        submitMessageButton.disabled = false;
        messageInputs.forEach(input => input.disabled = false);
    });
});