<?php
session_start();
header("Content-Type: application/json");
include '../database.php';
include '../../class/User.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($db);
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Step 1: Check for empty inputs
    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit;
    }

    // Step 2: Look for the user in the database
    $userData = $user->login($email);

    if (!$userData) {
        // ERROR: Email not found
        echo json_encode(["status" => "error", "message" => "No account found with that email address."]);
        exit;
    }

    // Step 3: Verify the password against the stored hash
    if (password_verify($password, $userData['password'])) {
        // SUCCESS: Set session variables
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['user_name'] = $userData['first_name'] . " " . $userData['last_name'];
        $_SESSION['user_type'] = $userData['user_type_id'];

        echo json_encode([
            "status" => "success", 
            "message" => "Welcome back, " . $userData['first_name'] . "!",
            "user_type" => $userData['user_type_id']
        ]);
    } else {
        // ERROR: Incorrect password
        echo json_encode(["status" => "error", "message" => "Incorrect password. Please try again."]);
    }
}
?>