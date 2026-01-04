<?php
header("Content-Type: application/json");
include 'database.php';
include '../class/Orders.php';
include '../class/Utility.php';

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$utility = new Utility($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['order_id'])) {
            // Fetch specific wine items for the modal
            $items = $order->getOrderItems($_GET['order_id']);
            echo json_encode(["status" => "success", "items" => $items]);
        } 
        elseif (isset($_GET['fetch_order_statuses'])) {
            // This uses the function you already have in Utility.php
            $statuses = $utility->getOrderStatuses(); 
            echo json_encode(["status" => "success", "statuses" => $statuses]);
        }
        elseif (isset($_GET['fetch_payment_statuses'])) {
            // Fetch all available order statuses
            $statuses = $utility->getPaymentStatuses();
            echo json_encode(["status" => "success", "statuses" => $statuses]);
        } 
        else {
            // Default: Fetch all orders for the table
            $orders = $order->getAllOrdersAdmin();
            echo json_encode(["status" => "success", "orders" => $orders]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        
        $success = true;

        if ($order_id > 0) {
            // Update Order Status if provided
            if (isset($data['status_id'])) {
                $success = $success && $order->updateOrderStatus($order_id, $data['status_id']);
            }
            // Update Payment Status if provided
            if (isset($data['payment_status_id'])) {
                $success = $success && $order->updatePaymentStatus($order_id, $data['payment_status_id']);
            }

            echo json_encode(["status" => $success ? "success" : "error"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid Order ID"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>