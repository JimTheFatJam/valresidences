document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/fetch-tenants.php")
        .then(response => response.json())
        .then(tenant_application => {
            const listingsContainer = document.getElementById("manage-tenants");
            listingsContainer.innerHTML = "";

            if (tenant_application.length === 0) {
                listingsContainer.innerHTML = "<p>No tenant applications found.</p>";
                return;
            }

            const table = document.createElement("table");
            table.classList.add("unit-table");

            const headerRow = document.createElement("tr");
            const headers = [
                "Application ID", "Unit", "First Name", "Last Name", "Email",
                "Application Status", "Created At"
            ];
            headers.forEach(headerText => {
                const th = document.createElement("th");
                th.textContent = headerText;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            tenant_application.forEach(application => {
                const row = document.createElement("tr");

                // Build cells in desired order
                const unitFormatted = `${application.subdivision_address}, Unit ${application.unit_number}`;
                const values = [
                    application.application_id,
                    unitFormatted,
                    application.first_name,
                    application.last_name,
                    application.user_email,
                    application.application_status,
                    application.created_at
                ];

                values.forEach((value, index) => {
                    const td = document.createElement("td");

                    if (index === 5) {
                        const select = document.createElement("select");
                        select.style.fontSize = "0.9em";

                        ["Pending", "Approved", "Declined"].forEach(status => {
                            const option = document.createElement("option");
                            option.value = status;
                            option.textContent = status;
                            if (status === value) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });

                        function updateSelectColor(selectElement) {
                            switch (selectElement.value) {
                                case "Pending":
                                    selectElement.style.backgroundColor = "#FFCC66"; // light yellow
                                    break;
                                case "Approved":
                                    selectElement.style.backgroundColor = "#8ED973"; // light green
                                    break;
                                case "Declined":
                                    selectElement.style.backgroundColor = "#FF5050"; // light red
                                    break;
                            }
                            selectElement.style.color = "#000";
                        }

                        updateSelectColor(select);
                        select.addEventListener("change", () => {
                            updateSelectColor(select);
                            updateApplicationStatus(application.application_id, select.value);
                            location.reload();
                        });

                        td.appendChild(select);
                    } else {
                        td.textContent = value;
                    }

                    row.appendChild(td);
                });

                table.appendChild(row);
            });

            listingsContainer.appendChild(table);
        })
        .catch(error => console.error("Error fetching tenant list:", error));
});

function updateApplicationStatus(applicationId, newStatus) {
    fetch("../backend/update-application-status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ application_id: applicationId, application_status: newStatus })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Status updated successfully!");
            } else {
                alert("Failed to update status.");
            }
        })
        .catch(error => {
            console.error("Error updating application status:", error);
        });
}