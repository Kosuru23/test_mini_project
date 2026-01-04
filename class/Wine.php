<?php
class Wine {
    private $conn;
    private $table = "wines";
    private $country_table = "country";
    private $wine_type_table = "wine_type";
    private $grape_variety_table = "grape_variety";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllWines() {
        $query = "SELECT wine_id, wine_name, wine_type_name, variety_name, region, name, alcohol_percentage, quantity, price, description, image_url, created_at, country.id AS country_id, wine_type.id AS wine_type_id, grape_variety.id AS grape_variety_id FROM " . $this->table . " 
        JOIN " . $this->country_table . " ON wines.country = country.id 
        JOIN " . $this->wine_type_table . " ON wines.wine_type = wine_type.id 
        JOIN " . $this->grape_variety_table . " ON wines.grape_variety = grape_variety.id 
        WHERE quantity > 0
        ORDER BY wine_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWineById($id) {
        $query = "SELECT wine_id, wine_name, wine_type, grape_variety, region, country, alcohol_percentage, description, quantity, price, image_url FROM " . $this->table . " WHERE wine_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addWine($wine_name, $wine_type, $grape_variety, $region, $country_id, $alcohol_percentage, $quantity, $price, $description, $image_url) {
        $query = "INSERT INTO " . $this->table . " (wine_name, wine_type, grape_variety, region, country, alcohol_percentage, quantity, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$wine_name, $wine_type, $grape_variety, $region, $country_id, $alcohol_percentage, $quantity, $price, $description, $image_url]);
    }

    public function updateWine($id, $wine_name, $wine_type, $grape_variety, $region, $country_id, $alcohol_percentage, $quantity, $price, $description, $image_url) {
        $query = "UPDATE " . $this->table . " SET wine_name = ?, wine_type = ?, grape_variety = ?, region = ?, country = ?, alcohol_percentage = ?, quantity = ?, price = ?, description = ?, image_url = ? WHERE wine_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$wine_name, $wine_type, $grape_variety, $region, $country_id, $alcohol_percentage, $quantity, $price, $description, $image_url, $id]);
    }

    public function deleteWine($id) {
        $query = "DELETE FROM " . $this->table . " WHERE wine_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>