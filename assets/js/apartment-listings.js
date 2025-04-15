document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            let listingsContainer = document.getElementById("apartment-listings");
            listingsContainer.innerHTML = "";

            for (const apartment of apartments) {
                const box = document.createElement("div");
                box.classList.add("apartment-box");

                const boxImage = document.createElement("div");
                boxImage.classList.add("apartment-image-container");

                // Fetch images for each apartment
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

                const boxText = document.createElement("div");
                boxText.classList.add("apartment-text");
                boxText.innerHTML = `
                    <h3>${apartment.subdivision_address}</h3>
                    <p>${apartment.address}</p>
                    <p><br>${apartment.apartment_type}</p>
                    <p>Vacant Units: ${apartment.units_vacant}</p>
                    <p>Php22,000.00–Php25,000.00</p>
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
                boxDetailsButton.addEventListener("click", function () {
                    window.location.href = `../pages/apartment-details.php?apartment_id=${apartmentId}`;
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