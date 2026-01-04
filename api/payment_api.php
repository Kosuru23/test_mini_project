<?php
header("Content-Type: application/json");
include 'database.php';
include '../class/Utility.php';

$database = new Database();
$db = $database->getConnection();

$utility = new Utility($db);
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['fetch_methods'])) {
            $data = $utility->getPaymentMethods();
            echo json_encode(["status" => "success", "methods" => $data]);
        } 
        elseif (isset($_GET['fetch_providers'])) {
            $data = $utility->getPaymentProviders();
            echo json_encode(["status" => "success", "providers" => $data]);
        } 
        elseif (isset($_GET['fetch_statuses'])) {
            $data = $utility->getPaymentStatuses();
            echo json_encode(["status" => "success", "statuses" => $data]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        try {
            $db->beginTransaction();
            // Update Order Payment Method
            $stmt1 = $db->prepare("UPDATE orders SET payment_method = ? WHERE order_id = ?");
            $stmt1->execute([$data['payment_method'], $data['order_id']]);

            // Insert into Payments Table
            $stmt2 = $db->prepare("INSERT INTO payments (order_id, payment_provider, payment_status, transaction_ref) VALUES (?, ?, ?, 'N/A')");
            $stmt2->execute([$data['order_id'], $data['payment_provider'], $data['payment_status']]);

            $db->commit();
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;
}
?>