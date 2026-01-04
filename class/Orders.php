<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function placeOrder($buyer_id, $total_amount, $payment_method, $items) {
        try {
            $this->conn->beginTransaction();

            // The columns in your DB are: buyer_id, total_amount, order_status, payment_method
            $query = "INSERT INTO orders (buyer_id, total_amount, order_status, payment_method) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            // Use 1 for 'Pending' status as defined in your order_status table
            $stmt->execute([$buyer_id, $total_amount, 1, $payment_method]); 
            
            $order_id = $this->conn->lastInsertId();

            // 2. Insert multiple items
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