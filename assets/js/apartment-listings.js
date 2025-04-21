document.addEventListener("DOMContentLoaded", function () {
    const userStatus = document.body.dataset.userStatus;
    console.log("User status is:", userStatus);
    
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            let listingsContainer = document.getElementById("apartment-listings");
            listingsContainer.innerHTML = "";

            const listingsWithPrices = await Promise.all(apartments.map(async (apartment) => {
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
            }));

            for (const { apartment, priceText } of listingsWithPrices) {
                const box = document.createElement("div");
                box.classList.add("apartment-box");

                const boxImage = document.createElement("div");
                boxImage.classList.add("apartment-image-container");

                // Fetch images for each apartment
                try {
                    const images = await fetch(`../backend/fetch-apartment-images.php?apartment_id=${apartment.apartment_id}`)
                        .then(response => response.json());

                    if (images.length > 0) {
                        const img = document.createElement("img");
                        img.src = `${images[0]}`; // Show the first image
                        img.alt = `${apartment.apartment_type} - ${apartment.subdivision_address}`;
                        img.style.width = "100%";
                        img.style.height = "100%";
                        boxImage.appendChild(img);
                    }
                } catch (error) {
                    console.error(`Error fetching images for apartment ${apartment.apartment_id}:`, error);
                }

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
                boxButtonsContainer.classList.add("apartment-buttons-container");

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

                const apartmentId = apartment.apartment_id;
                const boxDetailsButton = document.createElement("button");
                boxDetailsButton.textContent = "VIEW UNITS";
                boxDetailsButton.classList.add("plus-jakarta-sans");

                console.log("View Units redirecting to:", `../pages/${userStatus}-apartment-details.php?apartment_id=${apartmentId}`);
                boxDetailsButton.addEventListener("click", function () {
                    if (userStatus === "admin" || userStatus === "user" || userStatus === "tenant") {
                        window.location.href = `../pages/${userStatus}-apartment-details.php?apartment_id=${apartmentId}`;
                    } else {
                        window.location.href = `../pages/apartment-details.php?apartment_id=${apartmentId}`;
                    }
                });

                listingsContainer.appendChild(box);
                box.appendChild(boxImage);
                box.appendChild(boxText);
                box.appendChild(boxButtonsContainer);
                boxButtonsContainer.appendChild(boxMapButton);
                boxMapButton.appendChild(mapIcon);
                boxButtonsContainer.appendChild(boxDetailsButton);
            }
        }).catch(error => {
            console.error("Error fetching apartment listings:", error)
        });
});