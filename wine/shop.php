<?php include '../api/auth_checker/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine Collection - Shop</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/shop.css"> 
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">Fine Wine Estates</div>
            <ul class="menu">
                <li><a href="shop.php">Shop All</a></li>
                <li><a href="../user/my_orders.php">My Orders</a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Explore Our Collection</h1>
            <div class="search-filter">
                <input type="text" id="shopSearch" placeholder="Find a wine..." onkeyup="filterShop()">
                
                <div class="filter-controls" style="display: flex; gap: 10px; margin-top: 10px;">
                    <select id="filterCountry" onchange="filterShop()">
                        <option value="">All Countries</option>
                    </select>
                    
                    <select id="filterType" onchange="filterShop()">
                        <option value="">All Wine Types</option>
                    </select>
                    
                    <select id="filterVariety" onchange="filterShop()">
                        <option value="">All Grape Varieties</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="container">
            <div id="wineGrid" class="wine-grid"></div>
            
            <div id="paginationControls" class="pagination" style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
            </div>
        </div>
    </div>

    <div id="cartFooter" class="cart-footer" style="display: none;">
        <div class="cart-summary">
            <span id="cartCount">0 Items</span> | 
            <span id="cartTotal">$0.00</span>
        </div>
        <button class="checkout-btn" onclick="openCartModal()">View Cart & Checkout</button>
    </div>

    <div id="cartModal" class="modal">
        <div class="modal-content cart-modal">
            <span class="close" onclick="closeCartModal()">&times;</span>
            <h2>Your Shopping Cart</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Wine</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartTableBody"></tbody>
            </table>
            <div class="modal-footer">
                <h3>Total: <span id="modalTotal">$0.00</span></h3>
                <button class="buy-btn" onclick="checkout()">Place Order (COD)</button>
            </div>
        </div>
    </div>

    <script src="shop.js"></script>
</body>
</html>