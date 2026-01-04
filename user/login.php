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
    <title>Login - MIS</title>
    <link rel="stylesheet" href="../style/auth-style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Welcome Back</h2>
        <p class="subtitle">Please enter your credentials to access your account.</p>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="login_email">Email Address</label>
                <input type="email" id="login_email" placeholder="e.g. name@wine-service.com" required>
            </div>
            
            <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" id="login_password" placeholder="••••••••" required>
            </div>
            
            <button type="button" class="auth-btn" onclick="submitLogin()">Sign In</button>
        </form>
        
        <div class="auth-footer">
            New user? <a href="register.php">Create an account</a>
        </div>
    </div>

    <script src="auth.js"></script>
</body>
</html>