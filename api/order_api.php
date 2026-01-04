<?php
session_start();
header("Content-Type: application/json");

include 'database.php';
include '../class/Orders.php';

// Verify session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User session not found. Please log in."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($data['items'])) {
        $database = new Database();
        $db = $database->getConnection();
        $order = new Order($db);

        // Defensive: if payment_method is missing or 0, use 5 (Cash on Delivery) which exists in DB
        $payment_method = isset($data['payment_method']) && (int)$data['payment_method'] > 0 ? (int)$data['payment_method'] : 5;

        // order_status: default to Pending (1)
        $order_status = isset($data['order_status']) ? (int)$data['order_status'] : 1;

        $result = $order->placeOrder(
            $_SESSION['user_id'],
            $data['total_amount'],
            $payment_method,
            $data['items'],
            $order_status
        );
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "Empty cart items."]);
    }
}
?>