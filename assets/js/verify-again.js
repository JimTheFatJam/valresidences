document.getElementById("verify-again").addEventListener("click", function (event) {
    event.preventDefault();

    const urlParams = new URLSearchParams(window.location.search);
    const userEmail = urlParams.get("email"); // Extract email from URL
    let verifyAgainButton = document.getElementById("verify-again");
    let submitSubscribe = document.getElementById("submit-subscriber-email");

    $.ajax({
        method: "POST",
        url: "../backend/resend-verification.php",
        data: {
            userEmail: userEmail,
        },
        dataType: "json",
        beforeSend: function () {
            // **Enable Loading State ONLY if request starts**
            isLoading = true;
            verifyAgainButton.innerHTML = `<div class="spinner"></div>`;
            verifyAgainButton.disabled = true;
            submitSubscribe.disabled = true;
        }
    }).done(function (data) {
        if (data.result === -1) {
            alert("Error: " + data.message);
            isLoading = false;
            verifyAgainButton.innerHTML = "VERIFY AGAIN";
            verifyAgainButton.disabled = false;
            submitSubscribe.disabled = false;
        } else {
            alert(data.message);
            window.location.href = "../pages/index.php";
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown);
        alert("An error occurred. Please try again.");
        // **Reset UI on request failure**
        isLoading = false;
        verifyAgainButton.innerHTML = "VERIFY AGAIN";
        verifyAgainButton.disabled = false;
        submitSubscribe.disabled = false;
    });
});