document.addEventListener("DOMContentLoaded", () => {
    initializePaymentPage();
    loadPaymentMethods();   // Like loadCountryDropdowns()
    loadPaymentProviders(); // Like loadWinetypeDropdowns()
    loadPaymentStatuses();  // Like loadGrapeVarietyDropdowns()
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

// Dynamic Loader for Payment Methods
function loadPaymentMethods() {
    fetch("../api/payment_api.php?fetch_methods=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["payment_method_id", "editMethodId"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Payment Method</option>';
                    data.methods.forEach(method => {
                        let option = document.createElement("option");
                        option.value = method.method_id;
                        option.textContent = method.method_name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    });
}

function loadPaymentProviders() {
    fetch("../api/payment_api.php?fetch_providers=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["payment_provider_id", "editProviderId"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Payment Provider</option>';
                    data.providers.forEach(provider => {
                        let option = document.createElement("option");
                        option.value = provider.provider_id;
                        option.textContent = provider.provider_name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    });
}

function processPayment() {
    // Validation similar to addWine()
    const method = document.getElementById("payment_method_id").value;
    const provider = document.getElementById("payment_provider_id").value;
    const status = document.getElementById("payment_status_id").value;

    if (!method || !provider || !status) {
        alert("Please select all options.");
        return;
    }

    const paymentData = {
        order_id: localStorage.getItem("current_order_id"),
        payment_method: method,
        payment_provider: provider,
        payment_status: status
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
        }
    });
}

document.getElementById("paymentForm").addEventListener("submit", (e) => {
    e.preventDefault();
    processPayment();
});

document.addEventListener("DOMContentLoaded", () => {
    initializeSummary();
    loadDropdowns();
});
