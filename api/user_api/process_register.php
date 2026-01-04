<?php
header("Content-Type: application/json"); 
include '../database.php';
include '../../class/User.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($db);

    $first = $_POST['first_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $middle = $_POST['middle_name'] ?? null;
    $phone = $_POST['phone_number'] ?? null;

    // ERROR CHECK: Server-side validation
    if (empty($first) || empty($last) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Required fields are missing!"]);
        exit;
    }

    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $type = 2; // Default to Customer

    if ($user->register($first, $middle, $last, $email, $hashed_pass, $phone, $type)) {
        echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: Could not register."]);
    }
}
?>