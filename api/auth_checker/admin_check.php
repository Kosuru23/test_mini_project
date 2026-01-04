<?php
include 'auth_check.php';

// Check if the user is an Admin (Type 1)
if ($_SESSION['user_type'] != 1) {
    // If they are a Customer (Type 2) trying to see Admin pages, send them to the shop
    header("Location: ../../wine/shop.php");
    exit();
}
?>