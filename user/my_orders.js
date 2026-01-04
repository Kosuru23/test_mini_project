document.addEventListener("DOMContentLoaded", () => {
    loadOrders();
});

function loadOrders() {
    const container = document.getElementById("ordersList");
    if (!container) return;

    fetch("../api/order_api.php?fetch_user_orders=true")
        .then(res => res.json())
        .then(data => {
            container.innerHTML = "";

            if (data.status === "success" && data.orders.length > 0) {
                data.orders.forEach(order => {
                    const orderCard = createOrderCard(order);
                    container.appendChild(orderCard);
                });
            } else {
                container.innerHTML = `<p class="no-orders">You have no orders yet. <a href="../wine/shop.php">Start shopping!</a></p>`;
            }
        })
        .catch(err => {
            console.error("Error loading orders:", err);
            container.innerHTML = "<p>Error loading order history.</p>";
        });
}

function createOrderCard(order) {
    const card = document.createElement("div");
    card.className = "order-card";

    const total = parseFloat(order.total_amount).toFixed(2);
    const date = new Date(order.created_at).toLocaleDateString();
    const transactionNo = order.tracking_number ? order.tracking_number : "NOT ASSIGNED";
    const bottleCount = order.total_bottles || 0;

    const needsSetup = !order.address; 

    let actionButtons = "";

    if (needsSetup) {
        // Button to redirect back to the payment page to fill in details
        actionButtons = `
            <button class="buy-btn" onclick="redirectToPayment(${order.order_id}, ${total})">
                Set Payment & Shipping
            </button>
            <button class="cancel-btn" onclick="deleteOrder(${order.order_id})">Delete Order</button>
        `;
    } else {
        // Standard edit and delete buttons
        actionButtons = `
            <button class="edit-btn" onclick="openEditAddressModal(
                ${order.order_id}, '${order.address}', '${order.city}', '${order.postal_code}', '${order.country}'
            )">Edit Address</button>
            <button class="cancel-btn" onclick="deleteOrder(${order.order_id})">Delete Order</button>
        `;
    }

    card.innerHTML = `
        <div class="order-header">
            <div>
                <h3>Order #${order.order_id}</h3>
                <small><strong>Trans:</strong> ${transactionNo}</small>
            </div>
            <span class="status-badge status-${order.status_name.toLowerCase()}">${order.status_name}</span>
        </div>
        <div class="order-body">
            <p>
                <strong>Date:</strong> ${date} | 
                <strong>Bottles:</strong> ${bottleCount} | 
                <strong>Total:</strong> $${total}
            </p>
            <hr>
            ${needsSetup ? 
                `<p style="color: #d9534f;"><i>Details missing. Please complete your order.</i></p>` : 
                `<p><strong>Ship to:</strong> ${order.address}, ${order.city} (${order.postal_code})</p>`
            }
        </div>
        <div class="order-actions">
            ${actionButtons}
        </div>
    `;
    return card;
}

function redirectToPayment(orderId, total) {
    // Store the order info in localStorage so payment.php can pick it up
    localStorage.setItem("current_order_id", orderId);
    localStorage.setItem("current_order_total", total);
    window.location.href = "../wine/payment.php";
}

function deleteOrder(orderId) {
    if (!confirm("WARNING: This will permanently delete the order, shipping, and payment records. Continue?")) return;

    fetch(`../api/order_api.php?id=${orderId}`, {
        method: "DELETE"
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            location.reload(); 
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => console.error("Delete error:", err));
}

function openEditAddressModal(orderId, address, city, postal, country) {
    document.getElementById("editOrderId").value = orderId;
    document.getElementById("editAddress").value = address || "";
    document.getElementById("editCity").value = city || "";
    document.getElementById("editPostal").value = postal || "";
    document.getElementById("editCountry").value = country || "";
    document.getElementById("editModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

// Handle form submission
document.getElementById("editAddressForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const orderId = document.getElementById("editOrderId").value;
    const updateData = {
        order_id: orderId,
        address: document.getElementById("editAddress").value,
        city: document.getElementById("editCity").value,
        postal_code: document.getElementById("editPostal").value,
        country: document.getElementById("editCountry").value
    };

    fetch("../api/order_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updateData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Shipping address updated successfully!");
            closeEditModal();
            loadOrders(); // Refresh the list
        } else {
            alert("Failed to update address.");
        }
    })
    .catch(err => console.error("Error updating address:", err));
});
