<?php
require_once 'Database.php';

class Cart {
    private $conn;
    private $table = 'cart';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Add product to the cart
    public function addToCart($user_id, $product_id, $quantity) {
        // Check if the product is already in the cart for the user
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        // If the product exists in the cart, update the quantity
        if ($stmt->rowCount() > 0) {
            $query = "UPDATE " . $this->table . " SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);
            return $stmt->execute();
        } else {
            // Otherwise, insert a new row for this product in the cart
            $query = "INSERT INTO " . $this->table . " (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);
            return $stmt->execute();
        }
    }

    // Get all items in the user's cart
    public function getCartItems($user_id) {
        $query = "SELECT c.cart_id, p.name, p.price, c.quantity, p.image_url 
                  FROM " . $this->table . " c 
                  JOIN products p ON c.product_id = p.product_id 
                  WHERE c.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($cart_id, $quantity) {
        $query = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }


    public function removeFromCart($cart_id) {
        $query = "DELETE FROM cart WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    
    

    // Clear the user's cart
    public function clearCart($user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        // Clear the cart session
        unset($_SESSION['cart'][$user_id]);
        

        return $stmt->execute();
    }
}
?>
