<?php include '../api/auth_checker/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Your Payment</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/payment.css">
</head>
<body>
    <div class="container">
        <h1>Finalize Payment & Status</h1>

        <div class="shipping-section">
            <h3>Shipping Information</h3>
            <div class="form-group">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address" required placeholder="123 Wine St.">
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" required>
            </div>
            <div class="form-group">
                <label for="country_name">Country</label>
                <input type="text" id="country_name" name="country_name" required>
            </div>
        </div>
        <hr>
        
        <div id="paymentSummary" class="order-summary-card">
            <h3>Order #<span id="displayOrderId">--</span></h3>
            <p>Total: <strong id="displayTotal">$0.00</strong></p>
        </div>

        <form id="paymentForm" class="standard-form">
            <div class="form-group">
                <label>Payment Method</label>
                <select id="payment_method_id" required></select>
            </div>

            <div class="form-group">
                <label>Payment Provider</label>
                <select id="payment_provider_id" required></select>
            </div>

            <button type="submit" class="pay-btn">Update Order & Pay</button>
        </form>
    </div>

    <script src="payment.js"></script>
</body>
</html>