<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($first, $middle, $last, $email, $password, $phone, $type_id) {
        $query = "INSERT INTO " . $this->table . " 
                  (first_name, middle_name, last_name, email, password, phone_number, user_type_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$first, $middle, $last, $email, $password, $phone, $type_id]);
    }

    public function getUserByEmail($email) {
        $query = "SELECT user_id, first_name, last_name, password, user_type_id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email) {
        // Select necessary fields for verification and session
        $query = "SELECT user_id, first_name, last_name, password, user_type_id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>