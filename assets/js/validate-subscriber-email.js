document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("submit-subscriber-email").addEventListener("click", function (event) {
        event.preventDefault();

        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let emailInput = document.getElementById("subscriber_email").value.trim();

        // Check if the email is empty (should be handled by "required", but for extra safety)
        if (emailInput === "") {
            alert("Email cannot be empty!");
            return;
        } else if (!emailRegex.test(emailInput)) {
            alert("Please enter a valid email address with a correct domain (e.g., example@mail.com)");
            return;
        }
        $.ajax({
            method: "POST",
            url:  "../backend/store-subscriber-email.php",
            data: { "subscriber_email": emailInput },
            dataType: "json"
        }).done(function (data) {
            let result = data.result; // Convert response to number
            let str = '';

            if (result === 1) {
                str = 'Subscription successful! Email: ' + emailInput;
            } else if (result === 0) {
                str = 'Invalid email format.';
            } else if (result === -1) {
                str = 'Request failed. Please try again.';
            }

            alert(str);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("AJAX Request Failed");
            console.log("Status: ", textStatus);
            console.log("Error: ", errorThrown);
            console.log("Response Text: ", jqXHR.responseText);
            alert("AJAX function failed.\nStatus: " + textStatus + "\nError: " + errorThrown + "\nResponse: " + jqXHR.responseText);
        });
    });
});