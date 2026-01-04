<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // $order_status default set to 1 (Pending)
    public function placeOrder($buyer_id, $total_amount, $payment_method, $items, $order_status = 1) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO orders (buyer_id, total_amount, order_status, payment_method) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$buyer_id, $total_amount, $order_status, $payment_method]); 
            
            $order_id = $this->conn->lastInsertId();

            foreach ($items as $item) {
                $itemQuery = "INSERT INTO order_items (order_id, quantity, price_at_purchase, wine_id) VALUES (?, ?, ?, ?)";
                $itemStmt = $this->conn->prepare($itemQuery);
                $itemStmt->execute([$order_id, $item['quantity'], $item['price'], $item['wine_id']]);
            }

            $updateQuery = "UPDATE orders SET total_amount = GetOrderTotal(?) WHERE order_id = ?";
            $this->conn->prepare($updateQuery)->execute([$order_id, $order_id]);

            $this->conn->commit();
            return ["status" => "success", "order_id" => $order_id];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>