<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

class UserController
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new user
    public function register($data)
    {
        if (!isset($data['name'], $data['email'], $data['password'])) {
            echo json_encode(["status" => false, "message" => "Missing required fields"]);
            return;
        }

        $name = htmlspecialchars(strip_tags($data['name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'user';

        try {
            $query = "INSERT INTO users (name, email, password, role) 
                      VALUES (:name, :email, :password, :role)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":role", $role);

            if ($stmt->execute()) {
                echo json_encode(["status" => true, "message" => "User registered successfully"]);
            } else {
                echo json_encode(["status" => false, "message" => "User registration failed"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Login user
    public function login($data)
    {
        if (!isset($data['email'], $data['password'])) {
            echo json_encode(["status" => false, "message" => "Missing email or password"]);
            return;
        }

        $email = $data['email'];
        $password = $data['password'];

        try {
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                echo json_encode([
                    "status" => true,
                    "message" => "Login successful",
                    "user" => [
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "role" => $user['role']
                    ]
                ]);
            } else {
                echo json_encode(["status" => false, "message" => "Invalid email or password"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Logout
    public function logout()
    {
        session_destroy();
        echo json_encode(["status" => true, "message" => "Logged out successfully"]);
    }

    // Get all users (Admin only)
    public function getAllUsers()
    {
        requireAdmin();

        try {
            $query = "SELECT id, name, email, role, created_at FROM users";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => true, "data" => $users]);
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Get user by ID (Admin or self)
    public function getUserById($id)
    {
        requireAuth();

        if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            http_response_code(403);
            echo json_encode(["status" => false, "message" => "Forbidden"]);
            return;
        }

        try {
            $query = "SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo json_encode(["status" => true, "data" => $user]);
            } else {
                echo json_encode(["status" => false, "message" => "User not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Update user (Admin or self)
    public function updateUser($id, $data)
    {
        requireAuth();

        if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            http_response_code(403);
            echo json_encode(["status" => false, "message" => "Forbidden"]);
            return;
        }

        $fields = [];
        $params = [":id" => $id];

        if (!empty($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = htmlspecialchars(strip_tags($data['name']));
        }
        if (!empty($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = htmlspecialchars(strip_tags($data['email']));
        }
        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (empty($fields)) {
            echo json_encode(["status" => false, "message" => "No fields to update"]);
            return;
        }

        try {
            $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            if ($stmt->execute()) {
                echo json_encode(["status" => true, "message" => "User updated successfully"]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to update user"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // Delete user (Admin only)
    public function deleteUser($id)
    {
        requireAdmin();

        try {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);

            if ($stmt->execute()) {
                echo json_encode(["status" => true, "message" => "User deleted successfully"]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to delete user"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }
}
