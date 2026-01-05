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
        
        if ($order_id > 0) {
            try {
                $db->beginTransaction(); // Ensures both updates happen or none do

                // 1. Update Order Status
                if (isset($data['status_id']) && $data['status_id'] !== "") {
                    $order->updateOrderStatus($order_id, (int)$data['status_id']);
                }

                // 2. Update Payment Status
                if (isset($data['payment_status_id']) && $data['payment_status_id'] !== "") {
                    $order->updatePaymentStatus($order_id, (int)$data['payment_status_id']);
                }

                $db->commit();
                echo json_encode(["status" => "success"]);
            } catch (Exception $e) {
                $db->rollBack();
                // If the SQL trigger is the one failing, you will see the error here
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid Order ID"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>