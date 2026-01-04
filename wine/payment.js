document.addEventListener("DOMContentLoaded", () => {
    initializePaymentPage();
    loadPaymentMethods();
    loadPaymentProviders();
});

function initializePaymentPage() {
    const orderId = localStorage.getItem("current_order_id");
    const totalAmount = localStorage.getItem("current_order_total");

    if (!orderId) {
        alert("No active order found.");
        window.location.href = "shop.php";
        return;
    }
    document.getElementById("displayOrderId").innerText = orderId;
    document.getElementById("displayTotal").innerText = `$${parseFloat(totalAmount).toFixed(2)}`;
}

function loadPaymentMethods() {
    fetch("../api/payment_api.php?fetch_methods=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            const dropdown = document.getElementById("payment_method_id");
            if (dropdown) {
                dropdown.innerHTML = '<option value="">Select Payment Method</option>';
                data.methods.forEach(method => {
                    let option = document.createElement("option");
                    option.value = method.method_id;
                    option.textContent = method.method_name;
                    dropdown.appendChild(option);
                });
            }
        }
    });
}

function loadPaymentProviders() {
    fetch("../api/payment_api.php?fetch_providers=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            const dropdown = document.getElementById("payment_provider_id");
            if (dropdown) {
                dropdown.innerHTML = '<option value="">Select Payment Provider</option>';
                data.providers.forEach(provider => {
                    let option = document.createElement("option");
                    option.value = provider.provider_id;
                    option.textContent = provider.provider_name;
                    dropdown.appendChild(option);
                });
            }
        }
    });
}

function processPayment() {
    const method = document.getElementById("payment_method_id").value;
    const provider = document.getElementById("payment_provider_id").value;

    if (!method || !provider) {
        alert("Please select all options.");
        return;
    }

    const paymentData = {
        order_id: localStorage.getItem("current_order_id"),
        payment_method: parseInt(method),
        payment_provider: parseInt(provider)
        // Do NOT send payment_status; server will decide it
    };

    fetch("../api/payment_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(paymentData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Order updated successfully!");
            localStorage.clear();
            window.location.href = "receipt.php";
        } else {
            alert("Payment failed: " + (data.message || ""));
        }
    })
    .catch(err => {
        console.error(err);
        alert("Payment request failed.");
    });
}

document.getElementById("paymentForm").addEventListener("submit", (e) => {
    e.preventDefault();
    processPayment();
});