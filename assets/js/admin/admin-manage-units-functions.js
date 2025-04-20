isLoading = false;

// ADD UNIT

function openAddUnitPopup(apartmentId) {
    console.log(`Add unit for apartment ID: ${apartmentId}`);

    // Update popup UI to Add Mode
    const title = document.querySelector("#addUnitPopup h2");
    const button = document.querySelector(".add-unit-button-container button");
    title.innerHTML = "ADD UNIT";
    button.innerHTML = "SUBMIT";
    button.id = "submitNewUnit";
    button.setAttribute("onclick", `submitNewUnit(${apartmentId})`);

    // Always enable the Add‑submit button (undo any prior disable)
    button.disabled = false;

    // Clear input values AND remove any leftover data-original flags
    const textFields = [
        "unitNumber",
        "bedroomCount",
        "rentPrice",
        "floorCount",
        "tbCount",
        "monthAdvance",
        "livingArea",
        "leaseTerm",
        "monthDeposit",
    ];
    textFields.forEach(id => {
        const el = document.getElementById(id);
        el.value = "";
        el.removeAttribute("data-original");
    });

    // Clear select options (reset to default)
    const selectFields = [
        "parkingSpaceBool",
        "availabilityStatus",
        "petFriendlyBool",
        "furnishingStatus",
        "balconyBool"
    ];

    selectFields.forEach(id => {
        const selectEl = document.getElementById(id);
        if (selectEl) {
            selectEl.selectedIndex = 0; // Reset to "Select an option"
            selectEl.removeAttribute("data-original");
        }
    });

    // Clear file input
    const imgInput = document.getElementById("unitImages");
    imgInput.value = "";
    // (if you had attached change listeners for edit, you can skip/remove them)

    // Clear preview images
    const previewContainer = document.getElementById("previewUnitImages");
    previewContainer.innerHTML = "";
    previewContainer.style.display = "none";

    // Show popup
    document.getElementById("addUnitPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
}

function closeAddUnitPopup() {
    if (isLoading) return;
    document.getElementById("addUnitPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("previewUnitImages").style.display = "none";

    $(".error-message").remove();
    $(".addUnitForm input, .addUnitForm select").removeClass("error-border").val("").prop("selectedIndex", 0).removeAttr("data-original");
    $("#previewApartmentImages").empty();
}

// EDIT UNIT

function openEditUnitPopup(unitId) {
    console.log(`Edit unit with ID: ${unitId}`);

    // Update UI to Edit Mode
    const popupTitle = document.querySelector("#addUnitPopup h2");
    const button     = document.querySelector(".add-unit-button-container button");
    const imageContainer = document.getElementById("previewUnitImages");
    document.querySelector('#addUnitPopup .close_button').setAttribute('onclick', 'closeEditUnitPopup()');

    popupTitle.innerHTML = "EDIT UNIT";
    button.innerHTML     = "SAVE";
    button.id            = "submitEditUnit";
    button.setAttribute("onclick", `submitEditUnit(${unitId})`);
    button.disabled      = true;            // disable until a change
    imageContainer.innerHTML  = "";        
    imageContainer.style.display = "none";

    // Append delete button
    const buttonContainer = document.querySelector('.add-unit-button-container');
    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'plus-jakarta-sans';
    deleteBtn.id = 'deleteUnit';
    deleteBtn.textContent = 'DELETE';
    deleteBtn.onclick = function() {
        deleteUnit(unitId);
    };
    buttonContainer.appendChild(deleteBtn);

    // Show popup
    document.getElementById("addUnitPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";

    // Fetch and fill data
    $.ajax({
        method: "POST",
        url: "../backend/fetch-unit.php",
        data: { unitId },
        dataType: "json",
        success(response) {
            if (!response.success) {
                return alert("Failed to load apartment data.");
            }
            const data = response.data;

            // Prefill and store originals
            const fields = [
                { id: "unitNumber", value: data.unit_number },
                { id: "bedroomCount", value: data.bedroom_count },
                { id: "rentPrice", value: data.rent_price },
                { id: "floorCount", value: data.total_floors },
                { id: "tbCount", value: data.tb_count },
                { id: "monthAdvance", value: data.month_advance },
                { id: "monthDeposit", value: data.month_deposit },
                { id: "livingArea", value: data.living_area },
                { id: "leaseTerm", value: data.lease_term },
                { id: "availabilityStatus", value: data.availability_status },
                { id: "furnishingStatus", value: data.furnished_status },
                { id: "parkingSpaceBool", value: data.parking_space },
                { id: "petFriendlyBool", value: data.pet_friendly },
                { id: "balconyBool", value: data.balcony }
            ];            
            fields.forEach(f => {
                const el = document.getElementById(f.id);
                if (el) {
                    el.value = f.value;
                    el.dataset.original = f.value;
            
                    // Use "input" for text/number fields, "change" for selects
                    const isSelect = el.tagName === "SELECT";
                    el.addEventListener(isSelect ? "change" : "input", checkIfEditUnitFormChanged);
                } else {
                    console.warn(`Element with ID '${f.id}' not found.`);
                }
            });                       

            // File‑input listener
            const imgInput = document.getElementById("unitImages");
            imgInput.value = "";
            imgInput.addEventListener("change", checkIfEditUnitFormChanged);

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
            alert("Error fetching unit data.");
        }
    });
}

// Check for input change
function checkIfEditUnitFormChanged() {
    const fields = [
        "apartmentId",
        "unitNumber",
        "bedroomCount",
        "rentPrice",
        "floorCount",
        "tbCount",
        "monthAdvance",
        "monthDeposit",
        "livingArea",
        "leaseTerm",
        "availabilityStatus",
        "furnishingStatus",
        "parkingSpaceBool",
        "petFriendlyBool",
        "balconyBool"
    ];

    let hasChanged = false;

    for (const id of fields) {
        const el = document.getElementById(id);
        if (!el) continue;

        if (el.value !== el.dataset.original) {
            hasChanged = true;
            break;
        }
    }

    const images = document.getElementById("unitImages");
    if (images && images.files.length > 0) {
        hasChanged = true;
    }

    document.getElementById("submitEditUnit").disabled = !hasChanged;
}

function closeEditUnitPopup() {
    if (isLoading) return;
    document.getElementById("addUnitPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("previewApartmentImages").style.display = "none";
    document.getElementById("deleteUnit")?.remove();

    $(".error-message").remove();
    $(".addUnitForm input, .addUnitForm select").removeClass("error-border").val("");
    $("#previewUnitImages").empty();
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;
    closeAddUnitPopup();
    closeEditUnitPopup();
});


function submitNewUnit(apartmentId) {
    console.log(`Submit new unit for apartment ID: ${apartmentId}`);
    let fields = [
        { id: "unitNumber", label: "unitNumberLabel" },
        { id: "bedroomCount", label: "bedroomCountLabel" },
        { id: "rentPrice", label: "rentPriceLabel" },
        { id: "floorCount", label: "floorCountLabel" },
        { id: "tbCount", label: "tbCountLabel" },
        { id: "monthAdvance", label: "monthAdvanceLabel" },
        { id: "livingArea", label: "livingAreaLabel" },
        { id: "leaseTerm", label: "leaseTermLabel" },
        { id: "monthDeposit", label: "monthDepositLabel" },
        { id: "parkingSpaceBool", label: "parkingSpaceBoolLabel" },
        { id: "availabilityStatus", label: "availabilityStatusLabel" },
        { id: "petFriendlyBool", label: "petFriendlyBoolLabel" },
        { id: "furnishingStatus", label: "furnishingStatusLabel" },
        { id: "balconyBool", label: "balconyBoolLabel" },
        { id: "unitImages", label: "unitImagesLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitNewUnit");
    let inputs = document.querySelectorAll(".addUnitForm input");

    const unitNumber = document.getElementById("unitNumber").value.trim();
    const bedroomCount = document.getElementById("bedroomCount").value.trim();
    const rentPrice = document.getElementById("rentPrice").value.trim();
    const floorCount = document.getElementById("floorCount").value.trim();
    const tbCount = document.getElementById("tbCount").value.trim();
    const monthAdvance = document.getElementById("monthAdvance").value.trim();
    const livingArea = document.getElementById("livingArea").value.trim();
    const leaseTerm = document.getElementById("leaseTerm").value.trim();
    const monthDeposit = document.getElementById("monthDeposit").value.trim();
    const availabilityStatus = document.getElementById("availabilityStatus").value.trim();
    const furnishingStatus = document.getElementById("furnishingStatus").value.trim();

    const parkingSpaceBool = document.getElementById("parkingSpaceBool").value.trim() === "Yes" ? 1 : 0;
    const petFriendlyBool = document.getElementById("petFriendlyBool").value.trim() === "Yes" ? 1 : 0;
    const balconyBool = document.getElementById("balconyBool").value.trim() === "Yes" ? 1 : 0;

    const images = document.getElementById("unitImages").files;

    // Remove existing error indicators
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate fields dynamically
    fields.forEach(field => {
        let input = document.getElementById(field.id).value.trim();
        if (input === "" && field.id !== "unitImages") {
            $("#" + field.label).append('<span class="error-message" style="font-size: 0.8rem;"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isFieldEmpty = true;
        }
    });

    // Validation for unitImages (file input)
    if (images.length === 0) {
        $("#" + fields[14].label).append('<span class="error-message"> * Required</span>');
        $("#" + fields[14].id).addClass("error-border");
        isFieldEmpty = true;
    }

    if (isFieldEmpty) return;

    let formData = new FormData();
    formData.append("apartmentId", apartmentId);
    formData.append("unitNumber", unitNumber);
    formData.append("unitNumber", unitNumber);
    formData.append("bedroomCount", bedroomCount);
    formData.append("rentPrice", rentPrice);
    formData.append("floorCount", floorCount);
    formData.append("tbCount", tbCount);
    formData.append("monthAdvance", monthAdvance);
    formData.append("livingArea", livingArea);
    formData.append("leaseTerm", leaseTerm);
    formData.append("monthDeposit", monthDeposit);
    formData.append("availabilityStatus", availabilityStatus);
    formData.append("furnishingStatus", furnishingStatus);
    formData.append("parkingSpaceBool", parkingSpaceBool);
    formData.append("petFriendlyBool", petFriendlyBool);
    formData.append("balconyBool", balconyBool);

    // Append all selected images
    for (let i = 0; i < images.length; i++) {
        formData.append("unitImages[]", images[i]);
    }

    console.log(`Adding new unit for apartment ID: ${apartmentId}`);
    $.ajax({
        method: "POST",
        url: "../backend/add-new-unit.php",
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
        $(".addUnitForm input, .addUnitForm select").removeClass("error-border").val("");
        $("#previewUnitImages").empty();
        document.getElementById("previewUnitImages").style.display = "none";
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

function submitEditUnit(unitId) {
    console.log(`Save unit with ID: ${unitId}`);

    let fields = [
        { id: "unitNumber", label: "unitNumberLabel" },
        { id: "bedroomCount", label: "bedroomCountLabel" },
        { id: "rentPrice", label: "rentPriceLabel" },
        { id: "floorCount", label: "floorCountLabel" },
        { id: "tbCount", label: "tbCountLabel" },
        { id: "monthAdvance", label: "monthAdvanceLabel" },
        { id: "monthDeposit", label: "monthDepositLabel" },
        { id: "livingArea", label: "livingAreaLabel" },
        { id: "leaseTerm", label: "leaseTermLabel" },
        { id: "availabilityStatus", label: "availabilityStatusLabel" },
        { id: "furnishingStatus", label: "furnishingStatusLabel" },
        { id: "parkingSpaceBool", label: "parkingSpaceBoolLabel" },
        { id: "petFriendlyBool", label: "petFriendlyBoolLabel" },
        { id: "balconyBool", label: "balconyBoolLabel" },
        { id: "unitImages", label: "unitImagesLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitEditUnit");
    let inputs = document.querySelectorAll(".editUnitForm input, .editUnitForm select");

    // Get values
    const data = {
        unitId:           unitId,
        unitNumber:       document.getElementById("unitNumber").value.trim(),
        bedroomCount:     document.getElementById("bedroomCount").value.trim(),
        rentPrice:        document.getElementById("rentPrice").value.trim(),
        floorCount:       document.getElementById("floorCount").value.trim(),
        tbCount:          document.getElementById("tbCount").value.trim(),
        monthAdvance:     document.getElementById("monthAdvance").value.trim(),
        monthDeposit:     document.getElementById("monthDeposit").value.trim(),
        livingArea:       document.getElementById("livingArea").value.trim(),
        leaseTerm:        document.getElementById("leaseTerm").value.trim(),
        availabilityStatus: document.getElementById("availabilityStatus").value,
        furnishingStatus:   document.getElementById("furnishingStatus").value,
        parkingSpaceBool:   document.getElementById("parkingSpaceBool").value,
        petFriendlyBool:    document.getElementById("petFriendlyBool").value,
        balconyBool:        document.getElementById("balconyBool").value,
        unitImages:         document.getElementById("unitImages").files,
    };

    // Remove existing error indicators
    $(".error-message").remove();
    fields.forEach(field => $("#" + field.id).removeClass("error-border"));

    // Validate required fields (except images, optional)
    fields.forEach(field => {
        if (field.id === "unitImages") return;
        let value = document.getElementById(field.id).value.trim();
        if (value === "") {
            $("#" + field.label).append('<span class="error-message" style="font-size: 0.8rem;"> * Required</span>');
            $("#" + field.id).addClass("error-border");
            isFieldEmpty = true;
        }
    });

    if (isFieldEmpty) return;

    // Prepare form data
    let formData = new FormData();
    formData.append("unitId", unitId);
    for (let key in data) {
        if (key === "unitImages") {
            for (let i = 0; i < data.unitImages.length; i++) {
                formData.append("unitImages[]", data.unitImages[i]);
            }
        } else {
            formData.append(key, data[key]);
        }
    }

    // AJAX
    console.log("Saving unit data:", data.unitNumber);
    $.ajax({
        method: "POST",
        url: "../backend/edit-unit.php",
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

function deleteUnit(unitId) {
    // Create modal container
    const modal = document.createElement("div");
    modal.classList.add("modal-overlay");
    modal.innerHTML = `
        <div class="modal-box">
            <h2>Are you sure?</h2>
            <p>This action will <strong>permanently delete</strong> the unit, its images, and all related data.</p>
            <div class="modal-buttons">
                <button class="plus-jakarta-sans confirm-delete">Yes, Delete</button>
                <button class="plus-jakarta-sans cancel-delete">Cancel</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Add event listeners
    modal.querySelector(".cancel-delete").addEventListener("click", () => {
        modal.remove();
    });

    modal.querySelector(".confirm-delete").addEventListener("click", () => {
        fetch('../backend/delete-unit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ unitId: unitId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Unit deleted successfully.");
                // Optionally refresh or remove the apartment element from the DOM
                location.reload();
            } else {
                alert("Error deleting unit: " + data.message);
            }
        })
        .catch(err => {
            console.error("Delete request failed:", err);
            alert("Something went wrong.");
        });

        modal.remove();
    });
}