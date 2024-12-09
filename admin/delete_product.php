<?php
require_once '../classes/Product.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../views/login.php');
    exit();
}

$product = new Product();

// Check if the product ID is passed in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the database
    $product->delete($product_id);

    // Redirect back to the product list with a success message
    header("Location: products.php?deleted=true");
    exit();
} else {
    die('Product ID is missing.');
}
?>
