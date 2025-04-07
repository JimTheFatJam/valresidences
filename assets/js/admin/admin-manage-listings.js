document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-apartments.php")
        .then(response => response.json())
        .then(async apartments => {
            let listingsContainer = document.getElementById("manage-apartments");
            listingsContainer.innerHTML = "";

            for (const apartment of apartments) {
                const box = document.createElement("div");
                box.classList.add("apartment-box");

                const boxText = document.createElement("div");
                boxText.classList.add("apartment-text");
                boxText.innerHTML = `
                    <h3>${apartment.subdivision_address}</h3>
                    <p>${apartment.address}</p>
                    <p><br>${apartment.apartment_type}</p>
                    <p>Vacant Units: ${apartment.units_vacant}</p>
                    <p>Php21,000.00–Php25,000.00</p>
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
                    
                });

                listingsContainer.appendChild(box);
                box.appendChild(boxText);
                box.appendChild(boxButtonsContainer);
                boxButtonsContainer.appendChild(boxMapButton);
                boxMapButton.appendChild(mapIcon);
                boxButtonsContainer.appendChild(editUnitButton);
            }
        })
        .catch(error => console.error("Error fetching apartment listings:", error));
});