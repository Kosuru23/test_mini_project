<?php
session_start(); // Access the current session

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Completely destroy the session on the server
session_destroy();

// Redirect the user back to the login page
header("Location: ../../user/login.php");
exit;
?>