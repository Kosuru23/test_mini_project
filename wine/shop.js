// 1. Global state management
let cart = [];
let allWinesData = []; // Essential for stock validation

document.addEventListener("DOMContentLoaded", fetchShopWines);

function fetchShopWines() {
    fetch("../api/wine_api.php")
    .then(response => response.json())
    .then(data => {
        const wineGrid = document.getElementById("wineGrid");
        wineGrid.innerHTML = "";

        if (data.status === "success") {
            // CRITICAL: Store the wine data globally for use in addToCart and updateQty
            allWinesData = data.wine; 

            data.wine.forEach(wine => {
                const imgSrc = wine.image_url ? `../uploads/${wine.image_url}` : '../images/placeholder.png';
                const shortDesc = wine.description && wine.description.length > 100 
                    ? wine.description.substring(0, 100) + "..." 
                    : (wine.description || "No description available.");

                wineGrid.innerHTML += `
                <div class="wine-card">
                    <div class="wine-image">
                        <img src="${imgSrc}" alt="${wine.wine_name}">
                        <div class="alcohol-badge">${wine.alcohol_percentage}% ABV</div>
                    </div>
                    <div class="wine-info">
                        <span class="wine-category">${wine.wine_type_name}</span>
                        <h3>${wine.wine_name}</h3>
                        <p class="wine-variety">${wine.variety_name} | ${wine.name}</p>
                        <p class="wine-description">${shortDesc}</p>
                        <div class="wine-footer">
                            <div class="pricing">
                                <span class="wine-price">$${parseFloat(wine.price).toFixed(2)}</span>
                                <span class="stock-count ${wine.quantity <= 0 ? 'out' : ''}">
                                    ${wine.quantity > 0 ? wine.quantity + ' in stock' : 'Out of Stock'}
                                </span>
                            </div>
                            <button class="buy-btn" 
                                ${wine.quantity <= 0 ? 'disabled' : ''} 
                                onclick="addToCart(${wine.wine_id}, '${wine.wine_name.replace(/'/g, "\\'")}', ${wine.price})">
                                ${wine.quantity > 0 ? 'Add to Cart' : 'Sold Out'}
                            </button>
                        </div>
                    </div>
                </div>`;
            });
        }
    })
    .catch(error => console.error("Error fetching shop wines:", error));
}

// 2. Cart Logic with Stock Checking
function addToCart(wine_id, wine_name, price) {
    const wineInStock = allWinesData.find(w => w.wine_id === wine_id);
    let existingItem = cart.find(item => item.wine_id === wine_id);

    if (existingItem) {
        // Prevent adding more than available stock
        if (existingItem.quantity + 1 > wineInStock.quantity) {
            alert(`Sorry, only ${wineInStock.quantity} bottles of ${wine_name} are available.`);
            return;
        }
        existingItem.quantity += 1;
    } else {
        cart.push({ wine_id, wine_name, price: parseFloat(price), quantity: 1 });
    }
    
    updateCartUI();
    showFooter();
}

function updateQty(index, newQty) {
    const requestedQty = parseInt(newQty);
    if (isNaN(requestedQty) || requestedQty < 1) return;

    const wineId = cart[index].wine_id;
    const wineInStock = allWinesData.find(w => w.wine_id === wineId);

    // Dynamic stock validation in modal
    if (wineInStock && requestedQty > wineInStock.quantity) {
        alert(`Only ${wineInStock.quantity} bottles available.`);
        openCartModal(); 
        return;
    }

    cart[index].quantity = requestedQty;
    updateCartUI();
    openCartModal(); 
}

// 3. UI and Checkout Management
function checkout() {
    if (cart.length === 0) return alert("Your cart is empty!");

    const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const orderData = {
        total_amount: total,      // Maps to DB column
        payment_method: 5,        // Use existing method id (5 = Cash on Delivery) to satisfy FK
        order_status: 1,          // 1 = Pending (created as pending until payment is confirmed)
        items: cart 
    };

    fetch("../api/order_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            // Save order info to localStorage to use on the payment page
            localStorage.setItem("current_order_id", data.order_id);
            localStorage.setItem("current_order_total", total); 
            
            alert("Order created and set PENDING. Redirecting to payment...");
            window.location.href = "payment.php"; 
        } else {
            alert("Order creation failed: " + (data.message || ""));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to create order.');
    });
}

// UI Helper Functions
function updateCartUI() {
    const count = cart.reduce((acc, item) => acc + item.quantity, 0);
    const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const cartCountEl = document.getElementById("cartCount");
    const cartTotalEl = document.getElementById("cartTotal");
    if (cartCountEl) cartCountEl.innerText = `${count} Items`;
    if (cartTotalEl) cartTotalEl.innerText = `$${total.toFixed(2)}`;
    const modalTotal = document.getElementById("modalTotal");
    if (modalTotal) modalTotal.innerText = `$${total.toFixed(2)}`;
}

function showFooter() { const el = document.getElementById("cartFooter"); if (el) el.style.display = "flex"; }
function closeCartModal() { const el = document.getElementById("cartModal"); if (el) el.style.display = "none"; }
function removeItem(index) {
    cart.splice(index, 1);
    updateCartUI();
    if (cart.length === 0) {
        const el = document.getElementById("cartFooter");
        if (el) el.style.display = "none";
    }
}

function openCartModal() {
    const tbody = document.getElementById("cartTableBody");
    tbody.innerHTML = "";
    cart.forEach((item, index) => {
        tbody.innerHTML += `
        <tr>
            <td>${item.wine_name}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td><input type="number" value="${item.quantity}" min="1" onchange="updateQty(${index}, this.value)" style="width: 50px;"></td>
            <td>$${(item.price * item.quantity).toFixed(2)}</td>
            <td><button onclick="removeItem(${index})" class="delete-btn">Remove</button></td>
        </tr>`;
    });
    document.getElementById("cartModal").style.display = "flex";
}