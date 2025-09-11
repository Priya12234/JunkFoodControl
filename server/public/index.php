<?php

// header("Content-Type: application/json");

// require_once __DIR__ . '/../config/db.php';
// require_once __DIR__ . '/../controllers/UserController.php';


// $database = new Database();
// $conn = $database->getConnection();

// try {
//     // Use backticks for alias
//     $stmt = $conn->query("SELECT NOW() AS `server_time`");
//     $row = $stmt->fetch(PDO::FETCH_ASSOC);

//     echo json_encode([
//         "success" => true,
//         "message" => "Database connected successfully",
//         "server_time" => $row['server_time']
//     ]);
// } catch (PDOException $e) {
//     echo json_encode([
//         "success" => false,
//         "message" => "Database query failed: " . $e->getMessage()
//     ]);
// }


// // $controller = new UserController();

// // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// //     $data = json_decode(file_get_contents("php://input"), true);

// //     if ($_GET['action'] === 'register') {
// //         $controller->register($data);
// //     } elseif ($_GET['action'] === 'login') {
// //         $controller->login($data);
// //     }
// // } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'logout') {
// //     $controller->logout();
// // }

// $controller = new UserController();

// // Parse request
// $method = $_SERVER['REQUEST_METHOD'];
// $action = $_GET['action'] ?? null;
// $data   = json_decode(file_get_contents("php://input"), true);

// // === Auth Routes ===
// if ($action === 'register' && $method === 'POST') {
//     $controller->register($data);
// } elseif ($action === 'login' && $method === 'POST') {
//     $controller->login($data);
// } elseif ($action === 'logout' && $method === 'GET') {
//     $controller->logout();

//     // === User CRUD Routes (Admin only ideally) ===
// } elseif ($action === 'getUsers' && $method === 'GET') {
//     $controller->getAllUsers();
// } elseif ($action === 'getUser' && $method === 'GET' && isset($_GET['id'])) {
//     $controller->getUserById($_GET['id']);
// } elseif ($action === 'updateUser' && $method === 'PUT') {
//     $controller->updateUser($_GET['id'], $data);
// } elseif ($action === 'deleteUser' && $method === 'DELETE' && isset($_GET['id'])) {
//     $controller->deleteUser($_GET['id']);

//     // === Default / Invalid Route ===
// } else {
//     echo json_encode([
//         "success" => false,
//         "message" => "Invalid route or method"
//     ]);
// }

header("Content-Type: application/json");

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/FoodController.php';
require_once __DIR__ . '/../controllers/ConsumptionController.php'; // NEW

$database = new Database();
$conn = $database->getConnection();

// Quick DB test
try {
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
    exit;
}

// Controllers
$userController = new UserController();
$foodController = new FoodController();
$consumptionController = new ConsumptionController(); // NEW

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;
$data   = json_decode(file_get_contents("php://input"), true);

// =================== AUTH / USER ROUTES ===================
if ($action === 'register' && $method === 'POST') {
    $userController->register($data);
} elseif ($action === 'login' && $method === 'POST') {
    $userController->login($data);
} elseif ($action === 'logout' && $method === 'GET') {
    $userController->logout();

} elseif ($action === 'getAllUsers' && $method === 'GET') {
    $userController->getAllUsers();
} elseif ($action === 'getUserById' && $method === 'GET' && isset($_GET['id'])) {
    $userController->getUserById($_GET['id']);
} elseif ($action === 'updateUser' && $method === 'PUT' && isset($_GET['id'])) {
    $userController->updateUser($_GET['id'], $data);
} elseif ($action === 'deleteUser' && $method === 'DELETE' && isset($_GET['id'])) {
    $userController->deleteUser($_GET['id']);

// =================== FOOD ROUTES ===================
} elseif ($action === 'createFood' && $method === 'POST') {
    $foodController->createFood($data);
} elseif ($action === 'getAllFoods' && $method === 'GET') {
    $foodController->getAllFoods();
} elseif ($action === 'getFoodById' && $method === 'GET' && isset($_GET['id'])) {
    $foodController->getFoodById($_GET['id']);
} elseif ($action === 'updateFoodById' && $method === 'PUT' && isset($_GET['id'])) {
    $foodController->updateFoodById($_GET['id'], $data);
} elseif ($action === 'deleteFood' && $method === 'DELETE' && isset($_GET['id'])) {
    $foodController->deleteFoodById($_GET['id']);

// =================== CONSUMPTION ROUTES ===================
} elseif ($action === 'addConsumption' && $method === 'POST') {
    $consumptionController->addConsumption($data);
} elseif ($action === 'getMyConsumptions' && $method === 'GET') {
    $consumptionController->getMyConsumptions();
} elseif ($action === 'getAllConsumptions' && $method === 'GET') {
    $consumptionController->getAllConsumptions();
} elseif ($action === 'updateConsumption' && $method === 'PUT' && isset($_GET['id'])) {
    $consumptionController->updateConsumption($_GET['id'], $data);
} elseif ($action === 'deleteConsumption' && $method === 'DELETE' && isset($_GET['id'])) {
    $consumptionController->deleteConsumption($_GET['id']);

// =================== DEFAULT ===================
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid route or method"
    ]);
}
