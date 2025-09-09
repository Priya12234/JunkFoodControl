<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';

$userController = new UserController();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"), true);
    if(isset($data['action'])){
        switch($data['action']){
            case 'register':
                $userController->register($data);
                break;
            case 'login':
                $userController->login($data);
                break;
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Action not specified"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}