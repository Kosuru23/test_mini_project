<?php
class Utility {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getCountries() {
        $query = "SELECT id, name FROM country ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWineTypes() {
        $query = "SELECT id, wine_type_name FROM wine_type ORDER BY wine_type_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGrapeVarieties() {
        $query = "SELECT id, variety_name FROM grape_variety ORDER BY variety_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentProviders() {
        $query = "SELECT provider_id, provider_name FROM payment_provider ORDER BY provider_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentMethods() {
        $query = "SELECT method_id, method_name FROM payment_method ORDER BY method_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentStatuses() {
        // Explicitly order by status_id to ensure consistency between the DB and UI
        $query = "SELECT status_id, status_name FROM payment_status ORDER BY status_id ASC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrderStatuses() {
        // Explicitly order by status_id to ensure consistency between the DB and UI
        $query = "SELECT status_id, status_name FROM order_status ORDER BY status_id ASC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>