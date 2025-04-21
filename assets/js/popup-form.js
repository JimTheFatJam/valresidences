function openLoginPopup() {
    if (isLoading) return;
    document.getElementById("loginPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
    document.querySelector(".login-alert")?.remove();
}

function closeLoginPopup() {
    document.getElementById("loginPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("toggleLoginPassword").style.display = "none";
    // Remove existing error indicators
    $(".error-message").remove(); // Remove error messages
    $("#loginEmail, #loginPassword").removeClass("error-border").val(""); // Remove border and clear inputs
}

function openSignUpPopup() {
    if (isLoading) return;
    document.getElementById("signUpPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
}

function closeSignUpPopup() {
    if (isLoading) return; // Prevent closing if in loading state

    document.getElementById("signUpPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("toggleSignUpPassword").style.display = "none";

    $(".error-message").remove();
    $("#firstName, #lastName, #registerEmail, #signUpPassword, #confirmPassword").removeClass("error-border").val("");

    document.querySelectorAll(".signup-password-requirements p").forEach(req => {
        req.classList.remove("valid", "invalid");
    });
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;
    closeLoginPopup();
    closeSignUpPopup();
});