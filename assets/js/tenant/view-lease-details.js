document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-lease-details.php")
        .then(response => response.json())
        .then(result => {
            const container = document.querySelector(".lease-details-container");

            if (result.success) {
                const data = result.data;
                const yesNo = (val) => val == 1 ? "Yes" : "No";

                container.innerHTML = `
                    <table border="1" cellpadding="8" cellspacing="0" style="margin-bottom: 20px; width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Apartment</th>
                                <th>Unit No.</th>
                                <th>Total Floors</th>
                                <th>Living Area (sqm)</th>
                                <th>Bedrooms</th>
                                <th>Toilet & Baths</th>
                                <th>Balcony</th>
                                <th>Parking Space</th>
                                <th>Pet Friendly</th>
                                <th>Furnished</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${data.subdivision_address}</td>
                                <td>${data.unit_number}</td>
                                <td>${data.total_floors}</td>
                                <td>${data.living_area}</td>
                                <td>${data.bedroom_count}</td>
                                <td>${data.tb_count}</td>
                                <td>${yesNo(data.balcony)}</td>
                                <td>${yesNo(data.parking_space)}</td>
                                <td>${yesNo(data.pet_friendly)}</td>
                                <td>${data.furnished_status}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Lease Term (months)</th>
                                <th>Rent Price (Php)</th>
                                <th>Deposit (months)</th>
                                <th>Advance (months)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${data.lease_term}</td>
                                <td>${data.rent_price}</td>
                                <td>${data.month_deposit}</td>
                                <td>${data.month_advance}</td>
                            </tr>
                        </tbody>
                    </table>
                `;
            } else {
                container.innerHTML = `<p>${result.message || 'No lease information found.'}</p>`;
            }
        })
        .catch(error => {
            console.error("Error fetching lease details:", error);
            document.querySelector(".lease-details-container").innerHTML = `<p>Error loading lease details.</p>`;
        });
});