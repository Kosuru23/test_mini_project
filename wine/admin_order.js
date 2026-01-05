let allOrders = [];
let filteredOrders = [];
let currentPage = 1;
const recordsPerPage = 10;

document.addEventListener("DOMContentLoaded", () => {
    loadStatusDropdowns();
    loadAdminOrders();
})

function loadAdminOrders() {
    fetch("../api/admin_order_api.php")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            allOrders = data.orders;
            filteredOrders = [...allOrders]; // Initially, filtered is same as all
            displayOrders(1); 
        }
    });
}

function displayOrders(page) {
    const tableBody = document.getElementById("orderTableBody");
    if (!tableBody) return;

    currentPage = page;
    tableBody.innerHTML = "";

    // Calculate start and end for slicing
    const startIndex = (page - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const paginatedItems = filteredOrders.slice(startIndex, endIndex);

    paginatedItems.forEach((order, index) => {
        const date = new Date(order.created_at).toLocaleDateString();
        const statusClass = getStatusClass(order.order_status_name);
        const rowNumber = startIndex + index + 1; // Correct numbering across pages

        tableBody.innerHTML += `
        <tr>
            <td>${rowNumber}</td>
            <td><strong>${order.first_name} ${order.middle_name}, ${order.last_name}</strong></td>
            <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
            <td><span class="status-pill ${statusClass}">${order.order_status_name}</span></td>
            <td><span class="status-pill">${order.payment_status_name || 'Unpaid'}</span></td>
            <td><code>${order.tracking_number || 'Pending'}</code></td>
            <td>${date}</td>
            <td class="actions">
                <button class="edit-btn" onclick="viewOrderDetails(
                    ${order.order_id}, 
                    ${order.order_status}, 
                    ${order.payment_status || 0}
                )">View</button>
            </td>
        </tr>`;
    });

    renderPagination();
}

function renderPagination() {
    const paginationContainer = document.getElementById("pagination");
    const totalPages = Math.ceil(filteredOrders.length / recordsPerPage);
    
    let html = "";
    if (totalPages > 1) {
        // Previous Button
        html += `<button onclick="displayOrders(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&laquo; Prev</button>`;

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="${i === currentPage ? 'active' : ''}" onclick="displayOrders(${i})">${i}</button>`;
        }

        // Next Button
        html += `<button onclick="displayOrders(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Next &raquo;</button>`;
    }
    paginationContainer.innerHTML = html;
}

function filterOrders() {
    const nameSearch = document.getElementById("orderSearchBox").value.toUpperCase();
    const orderFilter = document.getElementById("orderFilterStatus").value.toUpperCase();
    const paymentFilter = document.getElementById("paymentFilterStatus").value.toUpperCase();

    filteredOrders = allOrders.filter(order => {
        const fullName = `${order.first_name} ${order.middle_name} ${order.last_name}`.toUpperCase();
        const orderStatus = (order.order_status_name || "").toUpperCase();
        const paymentStatus = (order.payment_status_name || "UNPAID").toUpperCase();

        const matchesName = fullName.includes(nameSearch);
        const matchesOrder = orderFilter === "" || orderStatus.includes(orderFilter);
        const matchesPayment = paymentFilter === "" || paymentStatus.includes(paymentFilter);

        return matchesName && matchesOrder && matchesPayment;
    });

    displayOrders(1);
}

function getStatusClass(status) {
    status = status.toLowerCase();
    // Match these exactly to your status_name list
    if (status === 'pending') return 'status-pending';     // ID 1
    if (status === 'processing') return 'status-processing'; // ID 2
    if (status === 'shipped') return 'status-shipped';       // ID 3
    if (status === 'delivered') return 'status-delivered';   // ID 4
    if (status === 'cancelled') return 'status-cancelled';   // ID 5
    if (status === 'refunded') return 'status-refunded';     // ID 6
    return 'status-default';
}
let currentEditingOrderId = null;

// admin_order.js
function viewOrderDetails(id, currentOrderStatusId, currentPaymentStatusId) {
    currentEditingOrderId = id;
    document.getElementById("displayOrderId").innerText = id;
    
    // 1. Fetch Order Items (existing logic)
    fetch(`../api/admin_order_api.php?order_id=${id}`)
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById("itemsContainer");
        container.innerHTML = data.items.map(item => `
            <div class="order-item-row" style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <img src="../uploads/${item.image_url}" width="40">
                <span>${item.wine_name} (x${item.quantity})</span>
            </div>
        `).join('');
    });

    // 2. Load Order Statuses and set current value
    fetch("../api/admin_order_api.php?fetch_order_statuses=true")
    .then(res => res.json())
    .then(data => {
        const drop = document.getElementById("updateStatusDropdown");
        drop.innerHTML = data.statuses.map(s => `<option value="${s.status_id}">${s.status_name}</option>`).join('');
        drop.value = currentOrderStatusId; // SET THE CORRECT OPTION
    });

    // 3. Load Payment Statuses and set current value
    fetch("../api/admin_order_api.php?fetch_payment_statuses=true")
    .then(res => res.json())
    .then(data => {
        const drop = document.getElementById("updatePaymentStatusDropdown");
        drop.innerHTML = data.statuses.map(s => `<option value="${s.status_id}">${s.status_name}</option>`).join('');
        if (currentPaymentStatusId > 0) {
            drop.value = currentPaymentStatusId; // SET THE CORRECT OPTION
        } else {
            drop.value = ""; // Default if no payment exists
        }
    });

    document.getElementById("detailsModal").style.display = "flex";
}

function submitStatusUpdate() {
    const statusId = document.getElementById("updateStatusDropdown").value;
    const paymentStatusId = document.getElementById("updatePaymentStatusDropdown").value;
    
    // Safety check: don't send if payment dropdown is empty unless intended
    if (!paymentStatusId) {
        alert("Please select a valid payment status.");
        return;
    }

    fetch("../api/admin_order_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            order_id: currentEditingOrderId, 
            status_id: statusId,
            payment_status_id: paymentStatusId 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("All statuses updated successfully!");
            closeDetailsModal();
            loadAdminOrders(); // This refreshes allOrders and filteredOrders
        } else {
            alert("Update Failed: " + data.message);
        }
    })
    .catch(err => console.error("Error:", err));
}

function closeDetailsModal() {
    document.getElementById("detailsModal").style.display = "none";
}

function loadStatusDropdowns() {
    // 1. Load Order Statuses (e.g., Pending, Shipped, Delivered)
    fetch("../api/admin_order_api.php?fetch_order_statuses=true")
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            const orderSelectors = ["orderFilterStatus", "updateStatusDropdown"];
            fillDropdowns(orderSelectors, data.statuses, "All Order Statuses");
        }
    });

    // 2. Load Payment Statuses (e.g., Paid, Refunded, Cancelled)
    fetch("../api/admin_order_api.php?fetch_payment_statuses=true")
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            const paymentSelectors = ["paymentFilterStatus", "updatePaymentStatusDropdown"];
            fillDropdowns(paymentSelectors, data.statuses, "All Payment Statuses");
        }
    });
}

// Helper function to keep code clean
function fillDropdowns(ids, statuses, placeholder) {
    ids.forEach(id => {
        let dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.innerHTML = `<option value="">${placeholder}</option>`;
            statuses.forEach(s => {
                let option = document.createElement("option");
                // Use status_id for IDs in the modal, but status_name for table filters
                option.value = id.includes('Filter') ? s.status_name : s.status_id;
                option.textContent = s.status_name;
                dropdown.appendChild(option);
            });
        }
    });
}