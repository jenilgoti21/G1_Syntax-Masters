<?php
session_start();
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = htmlspecialchars($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Thank You Container */
.thank-you-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    max-width: 500px;
    text-align: center;
}

.thank-you-container h1 {
    font-size: 2rem;
    color: #27ae60;
    margin-bottom: 20px;
}

.thank-you-container p {
    font-size: 1rem;
    line-height: 1.5;
    margin-bottom: 20px;
}

.thank-you-container a {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1rem;
    color: #fff;
    background-color: #3498db;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.thank-you-container a:hover {
    background-color: #2980b9;
}

/* Hidden Download Link */
#download-invoice {
    visibility: hidden;
    position: absolute;
}
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
</head>
<body>
    <div class="thank-you-container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order ID is <strong>#<?= $order_id ?></strong>.</p>
        <a href="../index.php">Return to Home</a>
    </div>
    <a id="download-invoice" style="display: none;" download href="../downloads/Invoice_<?= $order_id ?>.pdf">Download Invoice</a>
    <script>
        document.getElementById('download-invoice').click();
    </script>
</body>
</html>