document.addEventListener("DOMContentLoaded", () => {
    loadStatusDropdowns();
    loadAdminOrders();
});

function loadAdminOrders() {
    fetch("../api/admin_order_api.php")
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById("orderTableBody");
        if (!tableBody) return;

        tableBody.innerHTML = "";
        if (data.status === "success") {
            data.orders.forEach((order, index) => {
                const date = new Date(order.created_at).toLocaleDateString();
                // Use the correct alias from your PHP SQL
                const statusClass = getStatusClass(order.order_status_name);
                
                tableBody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${order.first_name} ${order.last_name}</strong></td>
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
        }
    });
}

function filterOrders() {
    const nameSearch = document.getElementById("orderSearchBox").value.toUpperCase();
    const orderFilter = document.getElementById("orderFilterStatus").value.toUpperCase();
    const paymentFilter = document.getElementById("paymentFilterStatus").value.toUpperCase();
    
    const rows = document.querySelector("#orderTableBody").getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        // Name is cell 1, Order Status is cell 3, Payment Status is cell 4
        const nameText = rows[i].cells[1].textContent.toUpperCase();
        const orderText = rows[i].cells[3].textContent.toUpperCase();
        const paymentText = rows[i].cells[4].textContent.toUpperCase();

        const matchesName = nameText.indexOf(nameSearch) > -1;
        const matchesOrder = orderFilter === "" || orderText.includes(orderFilter);
        const matchesPayment = paymentFilter === "" || paymentText.includes(paymentFilter);

        rows[i].style.display = (matchesName && matchesOrder && matchesPayment) ? "" : "none";
    }
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
            alert("Statuses updated successfully!");
            closeDetailsModal();
            loadAdminOrders();
        }
    });
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