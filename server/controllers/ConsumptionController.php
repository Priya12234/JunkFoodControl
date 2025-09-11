<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

class ConsumptionController
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ✅ Add a consumption log (only logged-in user)
    public function addConsumption($data)
    {
        requireAuth();

        if (!isset($data['food_item_id'], $data['quantity'])) {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $food_item_id = intval($data['food_item_id']);
        $quantity = intval($data['quantity']);
        $consumed_at = $data['consumed_at'] ?? date('Y-m-d H:i:s');

        try {
            $query = "INSERT INTO consumption_logs (user_id, food_item_id, quantity, consumed_at)
                      VALUES (:user_id, :food_item_id, :quantity, :consumed_at)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":food_item_id", $food_item_id);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":consumed_at", $consumed_at);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Consumption log added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to add consumption log"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Get current user’s logs
    public function getMyConsumptions()
    {
        requireAuth();
        $user_id = $_SESSION['user_id'];

        try {
            $query = "SELECT c.id, f.name AS food_name, f.calories, c.quantity,
                             (f.calories * c.quantity) AS total_calories, c.consumed_at
                      FROM consumption_logs c
                      JOIN food_items f ON c.food_item_id = f.id
                      WHERE c.user_id = :user_id
                      ORDER BY c.consumed_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["success" => true, "data" => $logs]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Get all logs (admin only)
    public function getAllConsumptions()
    {
        requireAdmin();

        try {
            $query = "SELECT c.id, u.name AS user_name, f.name AS food_name, f.calories,
                             c.quantity, (f.calories * c.quantity) AS total_calories, c.consumed_at
                      FROM consumption_logs c
                      JOIN users u ON c.user_id = u.id
                      JOIN food_items f ON c.food_item_id = f.id
                      ORDER BY c.consumed_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["success" => true, "data" => $logs]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Update a log (user updates own, admin can update any)
    public function updateConsumption($id, $data)
    {
        requireAuth();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 'user';
        $id = intval($id);

        try {
            // check ownership
            $checkQuery = "SELECT user_id FROM consumption_logs WHERE id = :id LIMIT 1";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([':id' => $id]);
            $log = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$log) {
                echo json_encode(["success" => false, "message" => "Log not found"]);
                return;
            }

            if ($log['user_id'] != $user_id && $role !== 'admin') {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "Unauthorized"]);
                return;
            }

            $fields = [];
            $params = [':id' => $id];

            if (isset($data['food_item_id'])) {
                $fields[] = "food_item_id = :food_item_id";
                $params[':food_item_id'] = intval($data['food_item_id']);
            }
            if (isset($data['quantity'])) {
                $fields[] = "quantity = :quantity";
                $params[':quantity'] = intval($data['quantity']);
            }
            if (isset($data['consumed_at'])) {
                $fields[] = "consumed_at = :consumed_at";
                $params[':consumed_at'] = $data['consumed_at'];
            }

            if (empty($fields)) {
                echo json_encode(["success" => false, "message" => "No fields to update"]);
                return;
            }

            $query = "UPDATE consumption_logs SET " . implode(", ", $fields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute($params)) {
                echo json_encode(["success" => true, "message" => "Consumption log updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update consumption log"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Delete a log (user can delete own, admin any)
    public function deleteConsumption($id)
    {
        requireAuth();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 'user';
        $id = intval($id);

        try {
            $checkQuery = "SELECT user_id FROM consumption_logs WHERE id = :id LIMIT 1";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([':id' => $id]);
            $log = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$log) {
                echo json_encode(["success" => false, "message" => "Log not found"]);
                return;
            }

            if ($log['user_id'] != $user_id && $role !== 'admin') {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "Unauthorized"]);
                return;
            }

            $query = "DELETE FROM consumption_logs WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Consumption log deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete consumption log"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }
}
