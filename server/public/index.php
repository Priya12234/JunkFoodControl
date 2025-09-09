<?php
header("Content-Type: application/json");

// Include DB config
require_once __DIR__ . '/../config/db.php';

// Create DB instance
$database = new Database();
$conn = $database->getConnection();

// Test query
try {
    $stmt = $conn->query("SELECT NOW() AS current_time");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        "success" => true,
        "message" => "Database connected successfully",
        "server_time" => $row['current_time']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database query failed: " . $e->getMessage()
    ]);
}
