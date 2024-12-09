<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['logged_in' => true, 'name' => $_SESSION['name']]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
}
?>
