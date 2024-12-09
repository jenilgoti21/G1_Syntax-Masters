<?php
require_once 'Database.php';

class Order {
    private $conn;
    private $table = 'orders';
    private $detailsTable = 'order_details';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createOrder($user_id, $total_amount, $cart_items) {
        try {
            $this->conn->beginTransaction();

            // Insert into orders table
            $query = "INSERT INTO " . $this->table . " (user_id, total_amount) VALUES (:user_id, :total_amount)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':total_amount', $total_amount);
            $stmt->execute();

            $order_id = $this->conn->lastInsertId();

            // Insert into order_details table
            $query = "INSERT INTO " . $this->detailsTable . " (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $this->conn->prepare($query);

            foreach ($cart_items as $item) {
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }

            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>
