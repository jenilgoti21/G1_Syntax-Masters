<?php

require_once 'Database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($name, $email, $password, $phone_number, $address) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (name, email, password, phone_number, address) 
                  VALUES (:name, :email, :password, :phone_number, :address)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':address', $address);

        return $stmt->execute();
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role']; // Store the role in the session

            // Set is_admin based on the user's role
            $_SESSION['is_admin'] = ($user['role'] === 'admin'); // Add this line

            return true;
        }
        return false;
    }
}
?>
