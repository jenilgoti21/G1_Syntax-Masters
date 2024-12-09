<?php
require_once '../classes/Product.php';

header('Content-Type: application/json');

$product = new Product();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = $product->readAll();
    echo json_encode($products);
}
?>
