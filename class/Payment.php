<?php
class Payment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function processTransaction($data) {
        try {
            $this->conn->beginTransaction();

            $orderId = $data['order_id'];
            $paymentMethod = isset($data['payment_method']) ? (int)$data['payment_method'] : 5; // Default COD

            // 1. Determine Statuses based on Payment Method
            $immediateMethods = [1, 2, 3, 4];
            $isImmediate = in_array($paymentMethod, $immediateMethods, true);
            $statuses = $this->getInternalStatusIds();

            if ($isImmediate) {
                $chosenPaymentStatus = $statuses['paid'] ?? 1;
                $newOrderStatus = 2; // Processing
            } else {
                $chosenPaymentStatus = $statuses['pending'] ?? 1;
                $newOrderStatus = 1; // Pending
            }

            // 2. Update Order Payment Method
            $stmt1 = $this->conn->prepare("UPDATE orders SET payment_method = ? WHERE order_id = ?");
            $stmt1->execute([$paymentMethod, $orderId]);

            // 3. Insert into Payments Table
            $stmt2 = $this->conn->prepare("INSERT INTO payments (order_id, payment_provider, payment_status) VALUES (?, ?, ?)");
            $stmt2->execute([$orderId, $data['payment_provider'], $chosenPaymentStatus]);

            // 4. Update order_status
            $stmt3 = $this->conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            $stmt3->execute([$newOrderStatus, $orderId]);

            $this->conn->commit();
            return ["status" => "success"];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    private function getInternalStatusIds() {
        $sql = "SELECT status_id, LOWER(status_name) AS lname FROM payment_status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = ['paid' => null, 'pending' => null];
        $paidCandidates = ['completed', 'paid', 'success'];
        $pendingCandidates = ['pending', 'unpaid'];

        foreach ($rows as $row) {
            if (in_array($row['lname'], $paidCandidates)) $results['paid'] = (int)$row['status_id'];
            if (in_array($row['lname'], $pendingCandidates)) $results['pending'] = (int)$row['status_id'];
        }
        return $results;
    }
}
?>