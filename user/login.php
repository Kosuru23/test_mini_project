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
    <link rel="stylesheet" href="../style/style.css">
    <style>
        .auth-container { max-width: 400px; margin: 100px auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .auth-container input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .auth-btn { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        <form id="loginForm">
            <input type="email" id="login_email" placeholder="Email Address" required>
            <input type="password" id="login_password" placeholder="Password" required>
            
            <button type="button" class="auth-btn" onclick="submitLogin()">Sign In</button>
        </form>
        <p>New user? <a href="register.php">Create an account</a></p>
    </div>

    <script src="auth.js"></script>
</body>
</html>