isLoading = false;  // Define isLoading here to avoid errors

document.addEventListener("DOMContentLoaded", async function () {
    const urlParams = new URLSearchParams(window.location.search);
    const apartmentID = urlParams.get("apartment_id");

    if (!apartmentID) {
        console.error("Apartment ID is missing from URL.");
        return;
    }

    const unitsContainer = document.getElementById("apartment-units-container");

    try {
        const response = await fetch(`../backend/fetch-apartment-details.php?apartment_id=${apartmentID}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch apartment details: ${response.status}`);
        }

        const data = await response.json();

        unitsContainer.innerHTML = ""; // Clear existing content

        if (data.length === 0) {
            unitsContainer.innerHTML = "<p>No units found for this apartment.</p>";
            return;
        }

        // Synchronous fetching of units
        for (const unit of data) {
            const unitCard = document.createElement("div");
            unitCard.classList.add("unit-card");

            // Unit Information
            const unitInfo = document.createElement("div");
            unitInfo.classList.add("unit-information");

            const formattedRent = `PHP ${parseFloat(unit.rent_price).toLocaleString("en-PH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;

            unitInfo.innerHTML = `
                <div class="unit-information-content">
                    <h3>UNIT ${unit.unit_number}</h3>
                    <br>
                    <p>${unit.total_floors} Floors</p>
                    <p>${unit.living_area} Sqm living area</p>
                    <p>${unit.bedroom_count} Bedrooms</p>
                    <p>${unit.tb_count} T&B</p>
                    <p>${unit.furnished_status}</p>
                    <p>${unit.balcony === 1 ? "With balcony" : "No balcony"}</p>
                    <p>${unit.parking_space === 1 ? "With parking" : "No parking"}</p>
                    <p>${unit.pet_friendly === 1 ? "Pet friendly" : " No pets allowed"}</p>
                    <br>
                    <p>Lease Term: ${unit.lease_term} ${unit.lease_term == 1 ? 'year' : 'years'}</p>
                    <p>Rent Price: ${formattedRent}</p>
                    <p>${unit.month_advance} ${unit.month_advance == 1 ? 'Month' : 'Months'} advance</p>
                    <p>${unit.month_deposit} ${unit.month_deposit == 1 ? 'Month' : 'Months'} deposit</p>
                    <br>
                    <div class="unit-information-status ${unit.availability_status.toLowerCase()}">
                        <p>${unit.availability_status.toUpperCase()}</p>
                    </div>
                </div>
            `;

            // Unit Buttons
            const unitButtons = document.createElement("div");
            unitButtons.classList.add("unit-information-buttons");

            const inquireButton = document.createElement("button");
            inquireButton.textContent = "INQUIRE UNIT";
            inquireButton.classList.add("plus-jakarta-sans");
            inquireButton.setAttribute("onclick", `openInquireUnitPopup(${unit.unit_id})`);

            const applyButton = document.createElement("button");
            applyButton.textContent = "APPLY NOW";
            applyButton.classList.add("plus-jakarta-sans");
            applyButton.setAttribute("onclick", `openApplyUnitPopup(${unit.unit_id})`);

            const userStatus = document.body.dataset.userStatus;
            const availabilityStatus = unit.availability_status;
            const isAvailable = availabilityStatus === "Available";

            // Default: show inquire button
            unitButtons.appendChild(inquireButton);

            // Conditionally show apply button for guests and users
            if (!userStatus || userStatus === "user") {
                if (isAvailable) {
                    unitButtons.appendChild(applyButton);
                } else {
                    applyButton.style.display = "none";
                    inquireButton.style.width = "100%";
                }
            }

            // For tenants: hide apply button and expand inquire button
            if (userStatus === "tenant") {
                applyButton.style.display = "none";
                inquireButton.style.width = "100%";
            }

            // Only append buttons if not admin
            if (userStatus !== "admin") {
                unitInfo.appendChild(unitButtons);
            }

            // Unit Image Container
            const unitImageContainer = document.createElement("div");
            unitImageContainer.classList.add("unit-images");
            unitImageContainer.id = `unit-images-${unit.unit_number}`;

            // Fetch Unit Images (Synchronous to maintain order)
            try {
                const imagesResponse = await fetch(`../backend/fetch-unit-images.php?apartment_id=${apartmentID}&unit_number=${unit.unit_number}`);
                if (!imagesResponse.ok) {
                    throw new Error(`Failed to fetch images for Unit ${unit.unit_number}: ${imagesResponse.status}`);
                }

                const images = await imagesResponse.json();

                const imageCount = images.length;
                // Add class based on image count
                if (imageCount === 1) {
                    unitImageContainer.classList.add("one");
                } else if (imageCount >= 2 && imageCount <= 4) {
                    unitImageContainer.classList.add("two", "three", "four");
                } else {
                    unitImageContainer.classList.add("five-or-more");
                }

                if (images.length > 0) {
                    images.forEach(image => {
                        const img = document.createElement("img");
                        img.src = `../${image}`;
                        img.alt = `Unit ${unit.unit_number}`;
                        img.style.width = "100%";
                        img.style.height = "auto";
                        img.onclick = function() {
                            openModal(this);
                        };
                        unitImageContainer.appendChild(img);
                    });
                } else {
                    unitImageContainer.innerHTML = "<p>No images available for this unit.</p>";
                }

            } catch (imageError) {
                console.error(`Error fetching unit images for Unit ${unit.unit_number}:`, imageError);
                unitImageContainer.innerHTML = "<p>Error loading images.</p>";
            }

            // Append to Unit Card
            unitCard.appendChild(unitInfo);
            unitCard.appendChild(unitImageContainer);
            unitsContainer.appendChild(unitCard);
        }
    } catch (error) {
        console.error("Error fetching apartment units:", error);
        unitsContainer.innerHTML = "<p>Error fetching apartment units.</p>";
    }
});


// INQUIRE UNIT FUNCTIONS

function openInquireUnitPopup(unitId) {
    console.log(`Inquiring unit with ID: ${unitId}`);

    // Show popup and overlay
    document.getElementById("unitInquiryPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("unitInquiryUnit").disabled = true;

    // Fetch apartment_id, unit_number, and subdivision_address
    fetch('../backend/fetch-unit-info.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ unitId: unitId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const unitText = `${data.subdivision_address} Unit ${data.unit_number}`;
                const emailText = document.body.dataset.userEmail || "";
                const input = document.getElementById("unitInquiryUnit");
                const email = document.getElementById("unitInquiryEmail");
                input.value = unitText;
                email.value = emailText;
                document.getElementById("submitUnitInquiry").onclick = () => submitUnitInquiry(data.unit_id);
            } else {
                console.error("Failed to fetch unit info:", data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function closeInquireUnitPopup() {
    if (isLoading) return;
    document.getElementById("unitInquiryPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";

    $(".error-message").remove();
    $("#unitInquiryUnit, #unitInquiryEmail, #unitInquiryUnitMessage").removeClass("error-border").val("");
}

document.getElementById("popupOverlay").addEventListener("click", function () {
    if (isLoading) return;

    if (document.getElementById("unitApplyPopup")) {
        closeApplyUnitPopup();
    }

    if (document.getElementById("unitInquiryPopup")) {
        closeInquireUnitPopup();
    }
});

// SUBMIT UNIT INQUIRY

function submitUnitInquiry(unitId) {
    console.log(`Submit inquiry for unit ID: ${unitId}`);

    let fields = [
        { id: "unitInquiryUnit", label: "unitInquiryUnitLabel" },
        { id: "unitInquiryEmail", label: "unitInquiryEmailLabel" },
        { id: "unitInquiryUnitMessage", label: "unitInquiryUnitMessageLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitUnitInquiry");
    let inputs = document.querySelectorAll(".unitInquiryForm input, .unitInquiryForm textarea");

    const unit = document.getElementById("unitInquiryUnit").value.trim();
    const email = document.getElementById("unitInquiryEmail").value.trim();
    const message = document.getElementById("unitInquiryUnitMessage").value.trim();

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

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address with a correct domain (e.g., example@mail.com)");
        return;
    }

    console.log("Sending Unit Inquiry:", unit, email, message);

    $.ajax({
        method: "POST",
        url: "../backend/send-unit-inquiry.php",
        data: { unitId: unitId, subject: unit, email: email, message: message },
        dataType: "json",
        beforeSend: function () {
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        },
        success: function (data) {
            alert(data.message);
            isLoading = false;
            submitButton.innerHTML = "SUBMIT";
            submitButton.disabled = false;
            document.getElementById("unitInquiryUnit").disabled = true;
            document.getElementById("unitInquiryEmail").disabled = false;
            document.getElementById("unitInquiryUnitMessage").disabled = false;
            $(".error-message").remove();
            $("#unitInquiryUnitMessage").removeClass("error-border").val("");
            closeInquireUnitPopup();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert("Request failed: " + textStatus + " - " + errorThrown);
            isLoading = false;
            console.error(jqXHR.responseText);
            submitButton.innerHTML = "SUBMIT";
            submitButton.disabled = false;
        }
    });
}


// APPLY UNIT FUNCTIONS

function openApplyUnitPopup(unitId) {
    console.log(`Apply for unit with ID: ${unitId}`);

    if (!isUserLoggedIn()) {
        openLoginPopup();

        const existingReason = document.getElementById("login-reason-message");
        if (existingReason) existingReason.remove();

        const loginForm = document.querySelector("#loginPopup form");
        const loginAlert = document.createElement("p");
        loginAlert.className = "login-alert";
        loginAlert.textContent = "You need to log in to apply for a unit.";

        loginForm.insertBefore(loginAlert, loginForm.querySelector(".signup-text"));

        return;
    }

    console.log("Proceeding with unit application.");

    // Show popup and overlay
    document.getElementById("unitApplyPopup").style.display = "block";
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("unitApplyUnit").disabled = true;

    // Fetch apartment_id, unit_number, and subdivision_address
    fetch('../backend/fetch-unit-info.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ unitId: unitId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const unitText = `${data.subdivision_address} Unit ${data.unit_number}`;
                const emailText = document.body.dataset.userEmail || "";
                const input = document.getElementById("unitApplyUnit");
                const email = document.getElementById("unitApplyEmail");
                input.value = unitText;
                email.value = emailText;
                document.getElementById("submitUnitApply").onclick = () => submitUnitApply(data.unit_id);
            } else {
                console.error("Failed to fetch unit info:", data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function isUserLoggedIn() {
    const userStatus = document.body.dataset.userStatus;
    return userStatus && userStatus !== '';
}

function closeApplyUnitPopup() {
    if (isLoading) return;
    document.getElementById("unitApplyPopup").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";

    $(".error-message").remove();
    $("#unitApplyUnit, #unitApplyEmail").removeClass("error-border").val("");
}

function submitUnitApply(unitId) {
    console.log(`Submit application for unit ID: ${unitId}`);

    let fields = [
        { id: "unitApplyUnit", label: "unitApplyUnitLabel" },
        { id: "unitApplyEmail", label: "unitApplyEmailLabel" },
    ];

    let isFieldEmpty = false;
    let submitButton = document.getElementById("submitUnitApply");
    let inputs = document.querySelectorAll(".unitApplyForm input, .unitApplyForm textarea");

    const unit = document.getElementById("unitApplyUnit").value.trim();
    const email = document.getElementById("unitApplyEmail").value.trim();

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

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address with a correct domain (e.g., example@mail.com)");
        return;
    }

    console.log("Sending Unit Application:", unit, email);

    $.ajax({
        method: "POST",
        url: "../backend/send-unit-application.php",
        data: { unitId: unitId, subject: unit, email: email },
        dataType: "json",
        beforeSend: function () {
            isLoading = true;
            submitButton.innerHTML = `<div class="spinner"></div>`;
            submitButton.disabled = true;
            inputs.forEach(input => input.disabled = true);
        },
        success: function (data) {
            alert(data.message);
            isLoading = false;
            submitButton.innerHTML = "SUBMIT";
            submitButton.disabled = false;
            closeApplyUnitPopup();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert("Request failed: " + textStatus + " - " + errorThrown);
            isLoading = false;
            console.error(jqXHR.responseText);
            submitButton.innerHTML = "SUBMIT";
            submitButton.disabled = false;
        }
    });
}