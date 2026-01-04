<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Existing placeOrder logic (Creates the base order)
    public function placeOrder($buyer_id, $total_amount, $payment_method, $items, $order_status = 1) {
        try {
            $this->conn->beginTransaction();
            $query = "INSERT INTO orders (buyer_id, total_amount, order_status, payment_method) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$buyer_id, $total_amount, $order_status, $payment_method]); 
            $order_id = $this->conn->lastInsertId();

            foreach ($items as $item) {
                $itemQuery = "INSERT INTO order_items (order_id, quantity, price_at_purchase, wine_id) VALUES (?, ?, ?, ?)";
                $this->conn->prepare($itemQuery)->execute([$order_id, $item['quantity'], $item['price'], $item['wine_id']]);
            }
            $this->conn->commit();
            return ["status" => "success", "order_id" => $order_id];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // NEW: Fetch orders with Tracking Number and Address
    public function cancelAndDeleteOrder($order_id, $buyer_id) {
        try {
            $this->conn->beginTransaction();

            // 1. Verify ownership
            $checkSql = "SELECT order_id FROM orders WHERE order_id = ? AND buyer_id = ?";
            $stmt = $this->conn->prepare($checkSql);
            $stmt->execute([$order_id, $buyer_id]);
            if (!$stmt->fetch()) {
                throw new Exception("Order not found or unauthorized.");
            }

            // 2. Delete Order Items (This triggers stock restoration)
            $delItems = "DELETE FROM order_items WHERE order_id = ?";
            $this->conn->prepare($delItems)->execute([$order_id]);

            // 3. Delete Shipping record
            $delShipping = "DELETE FROM shipping WHERE order_id = ?";
            $this->conn->prepare($delShipping)->execute([$order_id]);

            // 4. Delete Payments record
            $delPayments = "DELETE FROM payments WHERE order_id = ?";
            $this->conn->prepare($delPayments)->execute([$order_id]);

            // 5. Finally, delete the Order itself
            $delOrder = "DELETE FROM orders WHERE order_id = ?";
            $this->conn->prepare($delOrder)->execute([$order_id]);

            $this->conn->commit();
            return ["status" => "success", "message" => "Order and all related data deleted."];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Updated to allow editing regardless of status
     */
    public function updateShippingAddress($order_id, $buyer_id, $data) {
        $sql = "UPDATE shipping s 
                JOIN orders o ON s.order_id = o.order_id 
                SET s.address = ?, s.city = ?, s.postal_code = ?, s.country = ? 
                WHERE s.order_id = ? AND o.buyer_id = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['address'], 
            $data['city'], 
            $data['postal_code'], 
            $data['country'], 
            $order_id, 
            $buyer_id
        ]);
        return $result;
    }

    public function getUserOrders($buyer_id) {
        // We select payment_method from orders and address from shipping to check status
        $sql = "SELECT o.order_id, o.total_amount, o.created_at, o.order_status, o.payment_method,
                os.status_name, 
                sh.address, sh.city, sh.postal_code, sh.country, sh.tracking_number,
                -- SUBQUERY: Get the total count of items for this specific order
                (SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) as total_bottles
            FROM orders o 
            JOIN order_status os ON o.order_status = os.status_id 
            LEFT JOIN shipping sh ON o.order_id = sh.order_id 
            WHERE o.buyer_id = ? 
            ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$buyer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrdersAdmin() {
        $sql = "SELECT o.order_id, o.total_amount, o.created_at, o.order_status, p.payment_status,
                    u.first_name, u.last_name, 
                    os.status_name AS order_status_name, 
                    ps.status_name AS payment_status_name,
                    sh.address, sh.city, sh.tracking_number 
                FROM orders o 
                JOIN users u ON o.buyer_id = u.user_id 
                JOIN order_status os ON o.order_status = os.status_id 
                LEFT JOIN payments p ON o.order_id = p.order_id
                LEFT JOIN payment_status ps ON p.payment_status = ps.status_id
                LEFT JOIN shipping sh ON o.order_id = sh.order_id 
                WHERE sh.tracking_number IS NOT NULL
                ORDER BY o.created_at DESC"; // Removed the tracking_number restriction
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($order_id) {
        $sql = "SELECT oi.quantity, oi.price_at_purchase, w.wine_name, w.image_url 
                FROM order_items oi
                JOIN wines w ON oi.wine_id = w.wine_id
                WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($order_id, $status_id) {
        try {
            // We use order_status because that is the column name in your orders table
            $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([(int)$status_id, (int)$order_id]);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePaymentStatus($order_id, $payment_status_id) {
    try {
        $sql = "UPDATE payments SET payment_status = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([(int)$payment_status_id, (int)$order_id]);
    } catch (Exception $e) {
        return false;
    }
}
}
?>