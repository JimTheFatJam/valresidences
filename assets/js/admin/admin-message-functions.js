isLoading = false;

function openTenantAnnouncementPopup() {
    document.getElementById("tenantAnnouncementPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
}

function closeTenantAnnouncementPopup() {
    if (isLoading) return;
    document.getElementById("tenantAnnouncementPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";

    $(".error-message").remove();
    $("#tenantSubject, #tenantMessage").removeClass("error-border").val("");
}

function openVacancyAnnouncementPopup() {
    document.getElementById("vacancyAnnouncementPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
}

function closeVacancyAnnouncementPopup() {
    if (isLoading) return;
    document.getElementById("vacancyAnnouncementPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";

    $(".error-message").remove();
    $("#vacancySubject, #vacancyMessage").removeClass("error-border").val("");
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;
    closeTenantAnnouncementPopup();
    closeVacancyAnnouncementPopup();
});

function sendVacancyAnnouncement() {
    let fields = [
        { id: "vacancySubject", label: "vacancySubjectLabel" },
        { id: "vacancyMessage", label: "vacancyMessageLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitSubscriberAnnouncement");
    let inputs = document.querySelectorAll(".vacancyAnnouncementForm input, .vacancyAnnouncementForm textarea");

    const subject = document.getElementById("vacancySubject").value.trim();
    const message = document.getElementById("vacancyMessage").value.trim();

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

    console.log("Sending Vacancy Announcement:", subject, message);
    $.ajax({
        method: "POST",
        url: "../backend/send-vacancy-announcement.php",
        data: { subject: subject, message: message },
        dataType: "json",
        beforeSend: function () {
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        alert(data.message);
        isLoading = false;
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
        $(".error-message").remove();
        $("#vacancySubject, #vacancyMessage").removeClass("error-border").val("");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("Request failed: " + textStatus + " - " + errorThrown);
        isLoading = false;
        console.error(jqXHR.responseText);
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
}

function sendTenantAnnouncement() {
    let fields = [
        { id: "tenantSubject", label: "tenantSubjectLabel" },
        { id: "tenantMessage", label: "tenantMessageLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitTenantAnnouncement");
    let inputs = document.querySelectorAll(".tenantAnnouncementForm input, .tenantAnnouncementForm textarea");

    const subject = document.getElementById("tenantSubject").value.trim();
    const message = document.getElementById("tenantMessage").value.trim();

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

    console.log("Sending Tenant Announcement:", subject, message);
    $.ajax({
        method: "POST",
        url: "../backend/send-tenant-announcement.php",
        data: { subject: subject, message: message },
        dataType: "json",
        beforeSend: function () {
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        alert(data.message);
        isLoading = false;
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
        $(".error-message").remove();
        $("#tenantSubject, #tenantMessage").removeClass("error-border").val("");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("Request failed: " + textStatus + " - " + errorThrown);
        isLoading = false;
        console.error(jqXHR.responseText);
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
}