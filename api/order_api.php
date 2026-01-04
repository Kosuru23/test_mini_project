<?php
session_start();
header("Content-Type: application/json");
include 'database.php';
include '../class/Orders.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch order history for the current user
        $orders = $order->getUserOrders($_SESSION['user_id']);
        echo json_encode(["status" => "success", "orders" => $orders]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $order->placeOrder($_SESSION['user_id'], $data['total_amount'], 5, $data['items']);
        echo json_encode($result);
        break;

    case 'PUT':
        // Handle the Edit Address request
        $data = json_decode(file_get_contents("php://input"), true);
        $updated = $order->updateShippingAddress($data['order_id'], $data['address'], $data['city'], $data['postal_code'], $data['country']);
        echo json_encode(["status" => $updated ? "success" : "error"]);
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $result = $order->cancelAndDeleteOrder($_GET['id'], $_SESSION['user_id']);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => "error", "message" => "Order ID missing."]);
        }
        break;
}
?>