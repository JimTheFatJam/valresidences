isLoading = false;

function openAddApartmentPopup() {
    console.log("Add new apartment");
    document.getElementById("addApartmentPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("previewApartmentImages").style.display = "none";
}

function closeAddApartmentPopup() {
    if (isLoading) return;
    document.getElementById("addApartmentPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("previewApartmentImages").style.display = "none";

    $(".error-message").remove();
    $("#apartmentSubdivisionAddress, #apartmentAddress, #apartmentType, #apartmentMapURL, #apartmentImages").removeClass("error-border").val("");
    $("#previewApartmentImages").empty();
}

function openAddUnitPopup(apartmentId) {
    console.log(`Add unit for apartment ID: ${apartmentId}`);
}

function openEditApartmentPopup(apartmentId) {
    console.log(`Edit apartment with ID: ${apartmentId}`);
}

function openEditUnitPopup(unitId) {
    console.log(`Edit unit with ID: ${unitId}`);
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;
    closeAddApartmentPopup();
});

function submitNewApartment() {
    console.log('Submit new apartment');
    let fields = [
        { id: "apartmentSubdivisionAddress", label: "apartmentSubdivisionAddressLabel" },
        { id: "apartmentAddress", label: "apartmentAddressLabel" },
        { id: "apartmentType", label: "apartmentTypeLabel" },
        { id: "apartmentMapURL", label: "apartmentMapURLLabel" },
        { id: "apartmentImages", label: "apartmentImagesLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitNewApartment");
    let inputs = document.querySelectorAll(".addApartmentForm input");

    const subdivisionAddress = document.getElementById("apartmentSubdivisionAddress").value.trim();
    const address = document.getElementById("apartmentAddress").value.trim();
    const type = document.getElementById("apartmentType").value.trim();
    const mapURL = document.getElementById("apartmentMapURL").value.trim();
    const images = document.getElementById("apartmentImages").files;

    // Map URL regex
    const mapURLRegex = /^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})+([\/\w\-\.]*)*\/?$/;

    // Remove existing error indicators
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate fields dynamically
    fields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "" && field.id !== "apartmentImages") {
            $("#" + field.label).append('<span class="error-message"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isFieldEmpty = true;
        }
    });

    // Map URL validation
    if (mapURL && !mapURLRegex.test(mapURL)) {
        $("#" + fields[3].label).append('<span class="error-message"> * Invalid URL</span>');
        $("#" + fields[3].id).addClass("error-border");
        isFieldEmpty = true;
    }

    // Validation for apartmentImages (file input)
    if (images.length === 0) {
        $("#" + fields[4].label).append('<span class="error-message"> * Required</span>');
        $("#" + fields[4].id).addClass("error-border");
        isFieldEmpty = true;
    }

    if (isFieldEmpty) return;

    let formData = new FormData();
    formData.append("subdivisionAddress", subdivisionAddress);
    formData.append("address", address);
    formData.append("type", type);
    formData.append("mapURL", mapURL);

    // Append all selected images
    for (let i = 0; i < images.length; i++) {
        formData.append("apartmentImages[]", images[i]);
    }

    console.log("Adding new apartment:", subdivisionAddress);
    $.ajax({
        method: "POST",
        url: "../backend/add-new-apartment.php",
        data: formData,
        processData: false,
        contentType: false,
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
        $("#apartmentSubdivisionAddress, #apartmentAddress, #apartmentType, #apartmentMapURL, #apartmentImages").removeClass("error-border").val("");
        $("#previewApartmentImages").empty();
        document.getElementById("previewApartmentImages").style.display = "none";
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("Request failed: " + textStatus + " - " + errorThrown);
        isLoading = false;
        console.error(jqXHR.responseText);
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
}