document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            const listingsContainer = document.getElementById("manage-apartments");
            listingsContainer.innerHTML = "";

            const priceRangePromises = apartments.map(async (apartment) => {
                try {
                    const priceRange = await fetch(`../backend/fetch-price-range.php?apartment_id=${apartment.apartment_id}`)
                        .then(res => res.json());

                    const formatPrice = (price) => {
                        const numericPrice = parseFloat(price);
                        return isNaN(numericPrice)
                            ? null
                            : `PHP ${numericPrice.toLocaleString("en-PH", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}`;
                    };

                    const formattedMin = formatPrice(priceRange.min_price);
                    const formattedMax = formatPrice(priceRange.max_price);

                    let priceText;

                    if (formattedMin && formattedMax) {
                        priceText = priceRange.min_price === priceRange.max_price
                            ? formattedMin
                            : `${formattedMin} – ${formattedMax}`;
                    } else {
                        priceText = 'Price unavailable';
                    }

                    const unitDetails = await fetch(`../backend/fetch-apartment-details.php?apartment_id=${apartment.apartment_id}`)
                        .then(response => response.json());

                    return { apartment, priceText, unitDetails };
                } catch (error) {
                    console.error(`Error fetching price range or unit details for apartment ${apartment.apartment_id}:`, error);
                    return { apartment, priceText: 'Price unavailable', unitDetails: [] };
                }
            });

            const apartmentsWithPricesAndUnits = await Promise.all(priceRangePromises);

            apartmentsWithPricesAndUnits.forEach(({ apartment, priceText, unitDetails }) => {
                const box = document.createElement("div");
                box.classList.add("apartment-box");

                const boxText = document.createElement("div");
                boxText.classList.add("apartment-text");

                boxText.innerHTML = `
                    <h3>${apartment.subdivision_address}</h3>
                    <p>${apartment.address}</p>
                    <p><br>${apartment.apartment_type}</p>
                    <p>Vacant Units: ${apartment.units_vacant}</p>
                    <p>${priceText}</p>
                `;

                const boxButtonsContainer = document.createElement("div");
                boxButtonsContainer.classList.add("function-buttons-container");

                const apartmentMapAddress = apartment.map_address;
                const boxMapButton = document.createElement("button");
                const mapIcon = document.createElement("img");
                mapIcon.src = "../assets/icons/map_icon.svg";
                mapIcon.alt = "Map Icon";
                mapIcon.style.width = "18px";
                mapIcon.style.height = "18px";
                boxMapButton.addEventListener("click", function () {
                    window.open(apartmentMapAddress, "_blank");
                });

                const editApartmentButton = document.createElement("button");
                editApartmentButton.textContent = "EDIT LISTING";
                editApartmentButton.classList.add("plus-jakarta-sans");
                editApartmentButton.addEventListener("click", function () {
                    // Edit apartment
                    openEditApartmentPopup(apartment.apartment_id);
                });

                listingsContainer.appendChild(box);
                box.appendChild(boxText);
                box.appendChild(boxButtonsContainer);
                boxButtonsContainer.appendChild(boxMapButton);
                boxMapButton.appendChild(mapIcon);
                boxButtonsContainer.appendChild(editApartmentButton);

                let apartmentHeight = box.offsetHeight + 'px';

                const unitBox = document.createElement("div");
                unitBox.classList.add("unit-box");
                unitBox.style.height = apartmentHeight;

                let unitTable = `
                    <table class="unit-table">
                        <thead>
                            <tr>
                                <th>Edit</th>
                                <th>Unit #</th>
                                <th>Floors</th>
                                <th>Area</th>
                                <th>BR</th>
                                <th>T&B</th>
                                <th>Balcony</th>
                                <th>Parking</th>
                                <th>Pet Friendly</th>
                                <th>Lease</th>
                                <th>Rent</th>
                                <th>Deposit</th>
                                <th>Advance</th>
                                <th>Available</th>
                                <th>Furnished</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                unitDetails.forEach(unit => {
                    unitTable += `
                        <tr>
                            <td><button class="edit-unit-btn plus-jakarta-sans" onclick="openEditUnitPopup(${unit.unit_id})">EDIT UNIT</button></td>
                            <td>${unit.unit_number}</td>
                            <td>${unit.total_floors}</td>
                            <td>${unit.living_area} sqm</td>
                            <td>${unit.bedroom_count}</td>
                            <td>${unit.tb_count}</td>
                            <td>${unit.balcony == 1 ? 'Yes' : 'No'}</td>
                            <td>${unit.parking_space == 1 ? 'Yes' : 'No'}</td>
                            <td>${unit.pet_friendly == 1 ? 'Yes' : 'No'}</td>
                            <td>${unit.lease_term} ${unit.lease_term == 1 ? 'yr.' : 'yrs.'}</td>
                            <td>Php ${parseFloat(unit.rent_price).toLocaleString("en-PH", { minimumFractionDigits: 2 })}</td>
                            <td>${unit.month_deposit} ${unit.month_deposit == 1 ? 'mo.' : 'mos.'}</td>
                            <td>${unit.month_advance} ${unit.month_advance == 1 ? 'mo.' : 'mos.'}</td>
                            <td>${unit.availability_status}</td>
                            <td>${unit.furnished_status}</td>
                        </tr>
                    `;
                });

                unitTable += `
                        <tr>
                            <td>
                                <button class="add-unit-btn plus-jakarta-sans" data-apartment-id="${apartment.apartment_id}">
                                    ADD UNIT
                                </button>
                            </td>
                        </tr>
                    </tbody></table>
                `;
                
                unitBox.innerHTML = unitTable;

                unitBox.querySelector(".add-unit-btn").addEventListener("click", function () {
                    const apartmentId = this.getAttribute("data-apartment-id");
                    // Add unit for apartment
                    openAddUnitPopup(apartmentId);
                });

                const apartmentGroup = document.createElement("div");
                apartmentGroup.classList.add("apartment-group");
                apartmentGroup.appendChild(box);
                apartmentGroup.appendChild(unitBox);
                listingsContainer.appendChild(apartmentGroup);
            });

            const addApartmentBtn = document.createElement("button");
            addApartmentBtn.textContent = "ADD APARTMENT";
            addApartmentBtn.classList.add("add-apartment-btn", "plus-jakarta-sans");
            addApartmentBtn.addEventListener("click", function () {
                // Add apartment
                openAddApartmentPopup();
            });

            listingsContainer.appendChild(addApartmentBtn);
        }).catch(error => console.error("Error fetching apartment listings:", error));
});
