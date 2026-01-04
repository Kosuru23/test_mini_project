<?php
// Start the session to check user data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user_id exists in the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ../../user/login.php");
    exit();
}
?>