<?php
require_once __DIR__ . '/../config/db.php';
session_start();

class UserController
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register a new user
    public function register($data)
    {
        if (!isset($data['name'], $data['email'], $data['password'])) {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
            return;
        }

        $name = htmlspecialchars(strip_tags($data['name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "User registered successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "User registration failed"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Login user
    public function login($data)
    {
        if (!isset($data['email'], $data['password'])) {
            echo json_encode(["success" => false, "message" => "Missing email or password"]);
            return;
        }

        $email = $data['email'];
        $password = $data['password'];

        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Save login session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'] ?? 'user'; // default user role

            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "user" => [
                    "id" => $user['id'],
                    "name" => $user['name'],
                    "email" => $user['email'],
                    "role" => $user['role'] ?? 'user'
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        }
    }

    // Logout
    public function logout()
    {
        session_destroy();
        echo json_encode(["success" => true, "message" => "Logged out successfully"]);
    }
}
