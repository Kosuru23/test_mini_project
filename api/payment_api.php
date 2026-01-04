<?php
header("Content-Type: application/json");
include 'database.php';
include '../class/Utility.php';
include '../class/Payment.php';

$database = new Database();
$db = $database->getConnection();

$utility = new Utility($db);
$payment = new Payment($db); // New class instance
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['fetch_methods'])) {
            echo json_encode(["status" => "success", "methods" => $utility->getPaymentMethods()]);
        } 
        elseif (isset($_GET['fetch_providers'])) {
            echo json_encode(["status" => "success", "providers" => $utility->getPaymentProviders()]);
        } 
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        // Delegate all processing logic to the Payment class
        $result = $payment->processTransaction($data);
        echo json_encode($result);
        break;
}
?>