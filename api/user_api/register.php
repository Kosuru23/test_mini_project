<?php
header("Content-Type: application/json");
include '../database.php';
include '../../class/User.php'; // Correct path to your User class

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"), true);

if (
    !empty($data['first_name']) &&
    !empty($data['last_name']) &&
    !empty($data['email']) &&
    !empty($data['password'])
) {
    // Password hashing for security
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $result = $user->register(
        $data['first_name'],
        $data['middle_name'] ?? '',
        $data['last_name'],
        $data['email'],
        $hashedPassword,
        $data['phone_number'],
        $data['user_type_id']
    );

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Unable to create account. Email may already exist."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete data."]);
}
?>