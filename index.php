<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microwave Oven Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>



    <header>
        <nav class="navbar">
            <div class="navbar-container">
            <img src="./assets/images/logo.png" alt="logo" class="navbar-logo">
                <ul class="navbar-links">
                <li><a href="index.php" class="selected">Home</a></li>
                <li><a href="views/products.php">Products</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="views/cart.php">Cart</a></li>
                    <li><a href="views/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="views/login.php">Login</a></li>
                    <li><a href="views/register.php">Register</a></li>
                <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

     <!-- Main Content Section -->
     <main class="main-container">
        <section class="hero-section">
            <h2>Welcome to the Microwave Oven Store</h2>
            <p>Your one-stop shop for the best microwave ovens with top-notch features, durability, and style.</p>
            <a href="./views/products.php" class="btn btn-primary browse-btn">Browse Products</a>
        </section>
    </main>


    
</body>
</html>
