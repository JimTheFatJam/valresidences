isLoading = false;

// ADD APARTMENT

function openAddApartmentPopup() {
    console.log("Add new apartment");

    // Update popup UI to Add Mode
    const title = document.querySelector("#addApartmentPopup h2");
    const button = document.querySelector(".add-apartment-button-container button");
    title.innerHTML = "ADD APARTMENT";
    button.innerHTML = "SUBMIT";
    button.id = "submitNewApartment";
    button.setAttribute("onclick", "submitNewApartment()");

    // Always enable the Add‑submit button (undo any prior disable)
    button.disabled = false;

    // Clear input values AND remove any leftover data-original flags
    const textFields = [
      "apartmentSubdivisionAddress",
      "apartmentAddress",
      "apartmentType",
      "apartmentMapURL"
    ];
    textFields.forEach(id => {
      const el = document.getElementById(id);
      el.value = "";
      el.removeAttribute("data-original");
    });

    // Clear file input
    const imgInput = document.getElementById("apartmentImages");
    imgInput.value = "";
    // (if you had attached change listeners for edit, you can skip/remove them)

    // Clear preview images
    const previewContainer = document.getElementById("previewApartmentImages");
    previewContainer.innerHTML = "";
    previewContainer.style.display = "none";

    // Show popup
    document.getElementById("addApartmentPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
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

// EDIT APARTMENT

function openEditApartmentPopup(apartmentId) {
    console.log(`Edit apartment with ID: ${apartmentId}`);

    // Update UI to Edit Mode
    const popupTitle = document.querySelector("#addApartmentPopup h2");
    const button     = document.querySelector(".add-apartment-button-container button");
    const imageContainer = document.getElementById("previewApartmentImages");

    popupTitle.innerHTML = "EDIT APARTMENT";
    button.innerHTML     = "SAVE";
    button.id            = "submitEditApartment";
    button.setAttribute("onclick", `submitEditApartment(${apartmentId})`);
    button.disabled      = true;            // disable until a change
    imageContainer.innerHTML  = "";        
    imageContainer.style.display = "none";

    // Show popup
    document.getElementById("addApartmentPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";

    // Fetch and fill data
    $.ajax({
        method: "POST",
        url: "../backend/fetch-apartments.php",
        data: { apartmentId },
        dataType: "json",
        success(response) {
            if (!response.success) {
                return alert("Failed to load apartment data.");
            }
            const data = response.data;

            // Prefill and store originals
            const fields = [
                { id: "apartmentSubdivisionAddress", value: data.subdivision_address },
                { id: "apartmentAddress",            value: data.address             },
                { id: "apartmentType",               value: data.apartment_type      },
                { id: "apartmentMapURL",             value: data.map_address         }
            ];
            fields.forEach(f => {
                const el = document.getElementById(f.id);
                el.value = f.value;
                el.dataset.original = f.value;
                el.addEventListener("input", checkIfEditApartmentFormChanged);
            });

            // File‑input listener
            const imgInput = document.getElementById("apartmentImages");
            imgInput.value = "";
            imgInput.addEventListener("change", checkIfEditApartmentFormChanged);

            // Preview existing images
            if (data.images && data.images.length) {
                imageContainer.style.display = "flex";
                data.images.forEach(imgObj => {
                    const img = document.createElement("img");
                    img.src = imgObj.file_link;
                    imageContainer.appendChild(img);
                });
            }
        },
        error() {
            alert("Error fetching apartment data.");
        }
    });
}

// Check for input change
function checkIfEditApartmentFormChanged() {
    const subdivision = document.getElementById("apartmentSubdivisionAddress");
    const address     = document.getElementById("apartmentAddress");
    const type        = document.getElementById("apartmentType");
    const map         = document.getElementById("apartmentMapURL");
    const images      = document.getElementById("apartmentImages");

    const hasChanged =
        subdivision.value !== subdivision.dataset.original ||
        address.value     !== address.dataset.original     ||
        type.value        !== type.dataset.original        ||
        map.value         !== map.dataset.original         ||
        images.files.length > 0;

    document.getElementById("submitEditApartment").disabled = !hasChanged;
}

function closeEditApartmentPopup() {
    if (isLoading) return;
    document.getElementById("addApartmentPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("previewApartmentImages").style.display = "none";

    $(".error-message").remove();
    $("#apartmentSubdivisionAddress, #apartmentAddress, #apartmentType, #apartmentMapURL, #apartmentImages").removeClass("error-border").val("");
    $("#previewApartmentImages").empty();
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;
    closeAddApartmentPopup();
    closeEditApartmentPopup();
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
        location.reload();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("Request failed: " + textStatus + " - " + errorThrown);
        isLoading = false;
        console.error(jqXHR.responseText);
        submitButton.innerHTML = "SUBMIT";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
}


function submitEditApartment(apartmentId) {
    console.log(`Save apartment with ID: ${apartmentId}`);

    let fields = [
        { id: "apartmentSubdivisionAddress", label: "apartmentSubdivisionAddressLabel" },
        { id: "apartmentAddress", label: "apartmentAddressLabel" },
        { id: "apartmentType", label: "apartmentTypeLabel" },
        { id: "apartmentMapURL", label: "apartmentMapURLLabel" },
        { id: "apartmentImages", label: "apartmentImagesLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitEditApartment");
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
        // No new images selected, keep old images
        imagesToDelete = []; // Keep old images, no new ones to replace
    }

    if (isFieldEmpty) return;

    let formData = new FormData();
    formData.append("apartmentId", apartmentId);
    formData.append("subdivisionAddress", subdivisionAddress);
    formData.append("address", address);
    formData.append("type", type);
    formData.append("mapURL", mapURL);

    // Append new images if available
    for (let i = 0; i < images.length; i++) {
        formData.append("apartmentImages[]", images[i]);
    }

    // AJAX
    console.log("Saving apartment data:", subdivisionAddress);
    $.ajax({
        method: "POST",
        url: "../backend/edit-apartment.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        }
    }).done(function (data) {
        alert(data.message);
        submitButton.innerHTML = "SAVE";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
        $(".error-message").remove();
        location.reload();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("Request failed: " + textStatus + " - " + errorThrown);
        console.error(jqXHR.responseText);
        submitButton.innerHTML = "SAVE";
        submitButton.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
}