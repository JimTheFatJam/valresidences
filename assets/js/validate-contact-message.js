document.getElementById("submit-message").addEventListener("click", function (event) {
    event.preventDefault();

    let messageFields = [
        { id: "messageName", label: "messageNameLabel" },
        { id: "messageEmail", label: "messageEmailLabel" },
        { id: "messageBody", label: "messageBodyLabel" },
    ];

    let isMessageFieldsEmpty = false;
    let submitMessageButton = document.getElementById("submit-message");
    let messageInputs = document.querySelectorAll("#messageForm input, #messageForm textarea");

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

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let messageEmail = document.getElementById("messageEmail").value.trim();
    let messageName = document.getElementById("messageName").value.trim();
    let messageBody = document.getElementById("messageBody").value.trim();

    if (!emailRegex.test(messageEmail)) {
        alert("Please enter a valid email address with a correct domain (e.g., example@mail.com)");
        return;
    }

    $.ajax({
        method: "POST",
        url: "../backend/send-message.php",
        data: {
            messageEmail: messageEmail,
            messageName: messageName,
            messageBody: messageBody,
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