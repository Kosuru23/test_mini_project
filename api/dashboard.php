<?php
header("Content-Type: application/json");
include 'database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Single query to fetch all view data at once
    $stmt = $db->query("SELECT * FROM dashboard_stats_summary");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "stats" => [
            "total_wines" => $stats['total_wines'],
            "total_revenue" => number_format($stats['total_revenue'] ?? 0, 2),
            "low_stock" => $stats['low_stock'],
            "total_orders" => $stats['total_orders']
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>