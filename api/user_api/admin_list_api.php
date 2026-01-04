<?php
header("Content-Type: application/json");
include '../database.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all users with user_type_id = 1 (Admin)
        $query = "SELECT user_id, first_name, last_name, email FROM users WHERE user_type_id = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "admins" => $admins]);
        break;

    case 'DELETE':
        if (isset($_GET['delete_id'])) {
            $id = (int)$_GET['delete_id'];
            // Prevent deleting yourself
            if ($id == $_SESSION['user_id']) {
                echo json_encode(["status" => "error", "message" => "Cannot delete your own account."]);
                exit;
            }
            $query = "DELETE FROM users WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$id]);
            echo json_encode(["status" => $result ? "success" : "error", "message" => $result ? "Admin removed" : "Delete failed"]);
        }
        break;
}
?>