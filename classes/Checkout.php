<?php
require_once 'Database.php';
require_once 'Cart.php';
require_once 'Product.php';


class Order {
    private $conn;
    private $order_table = 'orders';
    private $checkout_table = 'checkout';
    private $order_details_table = 'order_details';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Insert order into the orders table
    public function createOrder($user_id, $total_amount) {
        $query = "INSERT INTO " . $this->order_table . " (user_id, total_amount) VALUES (:user_id, :total_amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->execute();
        return $this->conn->lastInsertId();  // Get the order ID
    }

    // Insert checkout details into the checkout table
    public function createCheckout($order_id, $user_id, $first_name, $last_name, $email, $mobile_no, $address, $city, $state, $zip_code, $payment_method, $card_holder_name, $card_number, $expiry_date, $cvv, $total_price, $tax, $grand_total) {
        $query = "INSERT INTO " . $this->checkout_table . " (order_id, user_id, first_name, last_name, email, mobile_no, address, city, state, zip_code, payment_method, card_holder_name, card_number, expiry_date, cvv, total_price, tax, grand_total) 
                  VALUES (:order_id, :user_id, :first_name, :last_name, :email, :mobile_no, :address, :city, :state, :zip_code, :payment_method, :card_holder_name, :card_number, :expiry_date, :cvv, :total_price, :tax, :grand_total)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile_no', $mobile_no);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':zip_code', $zip_code);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':card_holder_name', $card_holder_name);
        $stmt->bindParam(':card_number', $card_number);
        $stmt->bindParam(':expiry_date', $expiry_date);
        $stmt->bindParam(':cvv', $cvv);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':tax', $tax);
        $stmt->bindParam(':grand_total', $grand_total);
        return $stmt->execute();
    }


    public function createOrderDetails($order_id, $cart_Items) {
        foreach ($cart_Items as $item) {
            // Check if product_id is set and valid
            if (empty($item['product_id'])) {
                // Handle the error or skip this item
                echo "Error: product_id is missing for item: ";
                var_dump($item);
                continue; // Skip this item
            }
    
            // Proceed with the insert if product_id is valid
            $query = "INSERT INTO " . $this->order_details_table . " (order_id, product_id, quantity, price) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_id', $item['product_id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);
            $stmt->execute();
        }
    }



    
    
}
?>
