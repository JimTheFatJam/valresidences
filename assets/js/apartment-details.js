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
                    <p>Lease Term: ${unit.lease_term}</p>
                    <p>Rent Price: $${unit.rent_price}</p>
                    <p>${unit.month_advance} Month advance</p>
                    <p>${unit.month_deposit} Month deposit</p>
                    <br>
                    <div class="unit-information-status ${unit.availability_status.toLowerCase()}">
                        <p>STATUS: ${unit.availability_status.toUpperCase()}</p>
                    </div>
                </div>
            `;

            // Unit Buttons
            const unitButtons = document.createElement("div");
            unitButtons.classList.add("unit-information-buttons");

            const inquireButton = document.createElement("button");
            inquireButton.textContent = "INQUIRE UNIT";
            inquireButton.classList.add("plus-jakarta-sans");

            const applyButton = document.createElement("button");
            applyButton.textContent = "APPLY NOW";
            applyButton.classList.add("plus-jakarta-sans");

            unitButtons.appendChild(inquireButton);
            unitButtons.appendChild(applyButton);
            unitInfo.appendChild(unitButtons);

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
                        img.src = `../${image}`; // ✅ Fixed path
                        img.alt = `Unit ${unit.unit_number}`;
                        img.style.width = "100%";
                        img.style.height = "auto";
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