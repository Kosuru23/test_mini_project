<?php include '../api/auth_checker/admin_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Order Management</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">Wine Management System</div>
            <ul class="menu">
                <li><a href="index.php">Wine</a></li>
                <li><a href="admin_order.php" class="active">Orders</a></li>
                <li><a href="../user/create_account.php">Create Admin Account</a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div class="page-title">User Orders Management</div>
            <div class="search-filter">
                <input type="text" id="orderSearchBox" placeholder="Search by name..." onkeyup="filterOrders()">
                <select id="orderFilterStatus" onchange="filterOrders()"></select> 
                <select id="paymentFilterStatus" onchange="filterOrders()"></select>
            </div>
        </div>

    <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Payment Status</th> 
                    <th>Tracking</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orderTableBody"></tbody>
        </table>

        <div id="pagination" class="pagination-container"></div>
    </div>

    <div id="detailsModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closeDetailsModal()">&times;</span>
            <h3>Order Items - #<span id="displayOrderId"></span></h3>
            <hr>
            <div id="itemsContainer"></div>
            
            <div class="form-grid" style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label><strong>Order Status:</strong></label>
                    <select id="updateStatusDropdown" style="width: 100%;"></select>
                </div>
                <div>
                    <label><strong>Payment Status:</strong></label>
                    <select id="updatePaymentStatusDropdown" style="width: 100%;"></select>
                </div>
            </div>
            <button class="add-btn" style="margin-top: 15px; width: 100%;" onclick="submitStatusUpdate()">Save All Changes</button>
        </div>
    </div>

    <script src="admin_order.js"></script>
</body>
</html>