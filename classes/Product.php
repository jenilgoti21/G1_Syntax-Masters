<?php
require_once 'Database.php';

class Product {
    private $conn;
    private $table = 'products';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }


    public function createProduct($name, $description, $price, $stock, $image_url) {
        $query = "INSERT INTO products (name, description, price, stock, image_url)
                  VALUES (:name, :description, :price, :stock, :image_url)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':image_url', $image_url);
    
        $stmt->execute();
    }
    


    public function readAll($order = 'ASC') {
        $query = "SELECT * FROM " . $this->table;
        if ($order === 'ASC' || $order === 'DESC') {
            $query .= " ORDER BY price " . $order;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function readProduct($id) {
    $query = "SELECT * FROM " . $this->table . " WHERE product_id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return a single product's data if found
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function update($id, $name, $description, $price, $stock, $image_url) {
        $query = "UPDATE " . $this->table . " SET name=:name, description=:description, price=:price, stock=:stock, image_url=:image_url WHERE product_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':image_url', $image_url);

        return $stmt->execute();
    }
    

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE product_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
?>
