<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

class FoodController
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ✅ Create food item (Admin only)
    // @route POST /foods
    public function createFood($data)
    {
        requireAdmin();

        if (!isset($data['name'], $data['category'], $data['calories'])) {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
            return;
        }

        $name     = htmlspecialchars(strip_tags($data['name']));
        $category = htmlspecialchars(strip_tags($data['category']));
        $calories = intval($data['calories']);

        try {
            $query = "INSERT INTO food_items (name, category, calories, created_at) 
                      VALUES (:name, :category, :calories, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":category", $category);
            $stmt->bindParam(":calories", $calories);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Food item created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to create food item"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Get all food items
    // @route GET /foods
    public function getAllFoods()
    {
        requireAuth();

        try {
            $query = "SELECT * FROM food_items";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["success" => true, "data" => $foods]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Get food item by id
    // @route GET /foods/{id}
    public function getFoodById($id)
    {
        requireAuth();

        $id = intval($id);
        try {
            $query = "SELECT * FROM food_items WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $food = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($food) {
                echo json_encode(["success" => true, "data" => $food]);
            } else {
                echo json_encode(["success" => false, "message" => "Food item not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Update food item (Admin only)
    // @route PUT /foods/{id}
    public function updateFoodById($id, $data)
    {
        requireAdmin();

        $id = intval($id);
        $fields = [];
        $params = [':id' => $id];

        try {
            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = htmlspecialchars(strip_tags($data['name']));
            }
            if (isset($data['category'])) {
                $fields[] = "category = :category";
                $params[':category'] = htmlspecialchars(strip_tags($data['category']));
            }
            if (isset($data['calories'])) {
                $fields[] = "calories = :calories";
                $params[':calories'] = intval($data['calories']);
            }

            if (empty($fields)) {
                echo json_encode(["success" => false, "message" => "No fields to update"]);
                return;
            }

            $query = "UPDATE food_items SET " . implode(", ", $fields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Food item updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update food item"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    // ✅ Delete food item (Admin only)
    // @route DELETE /foods/{id}
    public function deleteFoodById($id)
    {
        requireAdmin();

        $id = intval($id);
        try {
            $query = "DELETE FROM food_items WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Food item deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete food item"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }
}
