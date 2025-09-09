<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';


$database = new Database();
$conn = $database->getConnection();

try {
    // Use backticks for alias
    $stmt = $conn->query("SELECT NOW() AS `server_time`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "message" => "Database connected successfully",
        "server_time" => $row['server_time']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database query failed: " . $e->getMessage()
    ]);
}


$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($_GET['action'] === 'register') {
        $controller->register($data);
    } elseif ($_GET['action'] === 'login') {
        $controller->login($data);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'logout') {
    $controller->logout();
}
