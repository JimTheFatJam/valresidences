document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            const listingsContainer = document.getElementById("manage-apartments");
            listingsContainer.innerHTML = "";

            const apartmentDataPromises = apartments.map(async (apartment) => {
                try {
                    const [priceRange, units] = await Promise.all([
                        fetch(`../backend/fetch-price-range.php?apartment_id=${apartment.apartment_id}`)
                            .then(res => res.json()),
                        fetch(`../backend/fetch-apartment-details.php?apartment_id=${apartment.apartment_id}`)
                            .then(res => res.json())
                    ]);

                    return { apartment, priceRange, units };
                } catch (error) {
                    console.error(`Error fetching data for apartment ${apartment.apartment_id}:`, error);
                    return { apartment, priceRange: null, units: [] };
                }
            });

            const allApartmentData = await Promise.all(apartmentDataPromises);

            allApartmentData.forEach(({ apartment, priceRange, units }) => {
                const formatPrice = (price) => {
                    const numericPrice = parseFloat(price);
                    return isNaN(numericPrice)
                        ? null
                        : `PHP ${numericPrice.toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}`;
                };

                const formattedMin = formatPrice(priceRange?.min_price);
                const formattedMax = formatPrice(priceRange?.max_price);

                let priceText;
                if (formattedMin && formattedMax) {
                    priceText = priceRange.min_price === priceRange.max_price
                        ? formattedMin
                        : `${formattedMin} – ${formattedMax}`;
                } else {
                    priceText = 'Price unavailable';
                }

                // Create apartment box
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

                const boxMapButton = document.createElement("button");
                const mapIcon = document.createElement("img");
                mapIcon.src = "../assets/icons/map_icon.svg";
                mapIcon.alt = "Map Icon";
                mapIcon.style.width = "18px";
                mapIcon.style.height = "18px";
                boxMapButton.appendChild(mapIcon);
                boxMapButton.addEventListener("click", () => {
                    window.open(apartment.map_address, "_blank");
                });

                const editUnitButton = document.createElement("button");
                editUnitButton.textContent = "EDIT LISTING";
                editUnitButton.classList.add("plus-jakarta-sans");
                editUnitButton.addEventListener("click", function () {
                    // edit unit logic
                });

                boxButtonsContainer.appendChild(boxMapButton);
                boxButtonsContainer.appendChild(editUnitButton);
                box.appendChild(boxText);
                box.appendChild(boxButtonsContainer);

                const unitBox = document.createElement("div");
                unitBox.classList.add("unit-box");
                unitBox.style.height = box.offsetHeight + 'px';

                let unitTable = `
                    <table class="unit-table">
                        <thead>
                            <tr>
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
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                units.forEach(unit => {
                    unitTable += `
                        <tr>
                            <td>${unit.unit_number}</td>
                            <td>${unit.total_floors}</td>
                            <td>${unit.living_area} sqm</td>
                            <td>${unit.bedroom_count}</td>
                            <td>${unit.tb_count}</td>
                            <td>${unit.balcony}</td>
                            <td>${unit.parking_space}</td>
                            <td>${unit.pet_friendly}</td>
                            <td>${unit.lease_term}</td>
                            <td>Php ${parseFloat(unit.rent_price).toLocaleString("en-PH", { minimumFractionDigits: 2 })}</td>
                            <td>${unit.month_deposit} mo.</td>
                            <td>${unit.month_advance} mo.</td>
                            <td>${unit.availability_status}</td>
                            <td>${unit.furnished_status}</td>
                            <td><button class="edit-unit-btn plus-jakarta-sans">EDIT UNIT</button></td>
                        </tr>
                    `;
                });

                unitTable += `</tbody></table>`;
                unitBox.innerHTML = unitTable;

                // Group apartment box and unit box
                const apartmentGroup = document.createElement("div");
                apartmentGroup.classList.add("apartment-group");
                apartmentGroup.appendChild(box);
                apartmentGroup.appendChild(unitBox);
                listingsContainer.appendChild(apartmentGroup);
            });
        })
        .catch(error => console.error("Error fetching apartment listings:", error));
});