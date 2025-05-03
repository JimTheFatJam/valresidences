document.addEventListener('DOMContentLoaded', () => {
    fetch('../backend/fetch-application-status.php')
      .then(res => res.json())
      .then(data => {
        const container = document.querySelector('.application-status-container');
  
        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = "<p>No applications found.</p>";
          return;
        }
  
        let table = document.createElement('table');
        table.classList.add('unit-table'); // Make sure you apply your CSS
  
        let thead = document.createElement('thead');
        thead.innerHTML = `
          <tr>
            <th>Application ID</th>
            <th>Unit</th>
            <th>Application Status</th>
            <th>Created At</th>
          </tr>
        `;
  
        let tbody = document.createElement('tbody');
  
        data.forEach(app => {
          const row = document.createElement('tr');
  
          const appIdCell = document.createElement('td');
          appIdCell.textContent = app.application_id;
  
          const unitCell = document.createElement('td');
          unitCell.textContent = `${app.subdivision_address} — Unit ${app.unit_number}`;
  
          const statusCell = document.createElement('td');
          statusCell.textContent = app.application_status;
  
          // Assign background color class based on status
          switch (app.application_status) {
            case "Pending":
              statusCell.classList.add("status-pending");
              break;
            case "Approved":
              statusCell.classList.add("status-approved");
              break;
            case "Declined":
              statusCell.classList.add("status-declined");
              break;
          }
  
          const createdAtCell = document.createElement('td');
          createdAtCell.textContent = new Date(app.created_at).toLocaleString();
  
          row.appendChild(appIdCell);
          row.appendChild(unitCell);
          row.appendChild(statusCell);
          row.appendChild(createdAtCell);
          tbody.appendChild(row);
        });
  
        table.appendChild(thead);
        table.appendChild(tbody);
        container.innerHTML = '';
        container.appendChild(table);
      })
      .catch(err => {
        console.error("Error loading applications:", err);
        document.querySelector('.application-status-container').innerHTML = "<p>Failed to load application status.</p>";
      });
  });  