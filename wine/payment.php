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