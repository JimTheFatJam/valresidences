document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            let listingsContainer = document.getElementById("manage-apartments");
            listingsContainer.innerHTML = "";

            // Collect all price range fetch promises in parallel
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
                
                    return { apartment, priceText };
                } catch (error) {
                    console.error(`Error fetching price range for apartment ${apartment.apartment_id}:`, error);
                    return { apartment, priceText: 'Price unavailable' };
                }                
            });

            // Wait for all the price range fetches to complete
            const apartmentsWithPrices = await Promise.all(priceRangePromises);

            apartmentsWithPrices.forEach(({ apartment, priceText }) => {
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
                mapIcon.style.width = "26px";
                mapIcon.style.height = "26px";
                boxMapButton.addEventListener("click", function () {
                    window.open(apartmentMapAddress, "_blank");
                });

                const editUnitButton = document.createElement("button");
                editUnitButton.textContent = "EDIT UNIT";
                editUnitButton.classList.add("plus-jakarta-sans");
                editUnitButton.addEventListener("click", function () {
                    // edit unit logic
                });

                listingsContainer.appendChild(box);
                box.appendChild(boxText);
                box.appendChild(boxButtonsContainer);
                boxButtonsContainer.appendChild(boxMapButton);
                boxMapButton.appendChild(mapIcon);
                boxButtonsContainer.appendChild(editUnitButton);
            });
        }).catch(error => console.error("Error fetching apartment listings:", error));
});
