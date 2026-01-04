<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user is ALREADY logged in, don't let them see the login/register page
if (isset($_SESSION['user_id'])) {
    // Redirect based on their role
    if ($_SESSION['user_type'] == 1) {
        header("Location: index.php");
    } else {
        header("Location: ../wine/shop.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MIS</title>
    <link rel="stylesheet" href="../style/auth-style.css">
    <style>
        .auth-container { max-width: 550px; } /* Slightly wider for registration */
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Create Account</h2>
        <p class="subtitle">Join and order wine in our system today.</p>
        
        <form id="registerForm">
            <div class="form-group">
                <label>Full Name</label>
                <div class="name-grid">
                    <input type="text" id="first_name" placeholder="First" required>
                    <input type="text" id="middle_name" placeholder="M.I.">
                    <input type="text" id="last_name" placeholder="Last" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="name@example.com" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" placeholder="+63 000 000 0000">
            </div>

            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="••••••••" required>
                </div>
                <div>
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="button" class="auth-btn register-btn" onclick="submitRegistration()">Complete Registration</button>
        </form>
        
        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script src="auth.js"></script>
</body>
</html>