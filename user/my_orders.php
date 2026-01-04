<?php include '../api/auth_checker/auth_check.php'; ?>
<?php 
// Ensure user is logged in before accessing the page
include '../api/auth_checker/auth_check.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Fine Wine Estates</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/shop.css">
    <link rel="stylesheet" href="../style/my_orders.css">
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">Fine Wine Estates</div>
            <ul class="menu">
                <li><a href="../wine/shop.php">Back to Shop</a></li>
                <li><a href="my_orders.php"><b>My Orders</b></a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Your Order History</h1>
            <p>Review and manage your wine reservations and purchases.</p>
        </div>

        <div id="ordersList">
            <div class="loading">Fetching your orders...</div>
        </div>
    </div>

    <div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Update Shipping Address</h2>
        <form id="editAddressForm">
            <input type="hidden" id="editOrderId">
            <div class="form-group">
                <label>Street Address</label>
                <input type="text" id="editAddress" required>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" id="editCity" required>
            </div>
            <div class="form-group">
                <label>Postal Code</label>
                <input type="text" id="editPostal" required>
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" id="editCountry" required>
            </div>
            <button type="submit" class="buy-btn">Save Changes</button>
        </form>
    </div>
</div>

    <script src="my_orders.js"></script>
</body>
</html>