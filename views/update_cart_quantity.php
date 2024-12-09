<?php
session_start();
require_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    if ($quantity >= 1) {
        $cart = new Cart();
        if ($cart->updateQuantity($cart_id, $quantity)) {
            echo "Quantity updated successfully";
        } else {
            echo "Failed to update quantity";
        }
    } else {
        echo "Invalid quantity";
    }
} else {
    echo "Invalid request";
}
?>
