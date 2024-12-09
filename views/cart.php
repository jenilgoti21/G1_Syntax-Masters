<?php
session_start();
require_once '../classes/Cart.php';
require_once '../classes/Product.php';  // Include the Product class to fetch product details

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle Add to Cart action
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $user_id = $_SESSION['user_id'];
    $quantity = 1; // Default quantity (you can change this logic as needed)

    $cart = new Cart();
    $cart->addToCart($user_id, $product_id, $quantity);

    header("Location: cart.php");
    exit;
}

// Fetch cart items for the logged-in user
$cart = new Cart();
$cart_items = $cart->getCartItems($_SESSION['user_id']);

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $updated_quantities = $_POST['quantity'];
    $cart_id = $_POST['cart_id'];

    foreach ($updated_quantities as $index => $quantity) {
        if ($quantity > 0) {
            $cart->updateQuantity($cart_id[$index], $quantity);
        }
    }

    // Redirect to the same page to see the updated cart
    header("Location: cart.php");
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_cart_id'])) {
    $cart_id_to_remove = intval($_POST['remove_cart_id']);
    $cart = new Cart();
    if ($cart->removeFromCart($cart_id_to_remove)) {
        // Redirect to the same page to refresh the cart
        header("Location: cart.php");
        exit;
    } else {
        echo "<p style='color: red;'>Failed to remove item from the cart.</p>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to external CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: rgb(97 86 142);
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        .cart-item-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .quantity-controls {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .quantity-controls button {
            padding: 5px 10px;
            background-color: rgb(93 78 153);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .quantity-controls button:hover {
            background-color: rgba(237, 233, 254, 1);
            color: black !important;
        }

        .checkout-button {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            text-align: center;
        }

        .checkout-button:hover {
            background-color: rgba(237, 233, 254, 1);
            color: black !important;
        }

        .empty-cart-message {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 20px;
        }

        .selected {
            background-color: rgba(91, 33, 182, 1);
            border-radius: 0.5rem;
            color: white !important;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #5b21b6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .center {
            text-align: center;
        }

        .devided {
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>

<body>


    <header>
        <nav class="navbar">
            <div class="navbar-container">
                <img src="../assets/images/logo.png" alt="logo" class="navbar-logo">
                <ul class="navbar-links">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="cart.php" class="selected">Cart</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

    <div class="cart-container">
        <h1>Your Cart</h1>

        <?php if (empty($cart_items)) { ?>
            <p class="empty-cart-message">Your cart is empty. Add some products to your cart!</p>
            <div class="center">
            <a href="./products.php" class="btn">Return to Product</a>
            </div>
        <?php } else { ?>
            <form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_price = 0;
                        foreach ($cart_items as $item) {
                            $total = $item['price'] * $item['quantity'];
                            $total_price += $total;
                            ?>
                            <tr>
                                <td class="cart-item-image">
                                    <?php if (!empty($item['image_url'])) { ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($item['image_url']); ?>"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php } else { ?>
                                        <p>No image available</p>
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <div class="quantity-controls">
                                        <button type="button"
                                            onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">-</button>
                                        <input type="number" name="quantity[]" value="<?php echo $item['quantity']; ?>" min="1"
                                            id="quantity-<?php echo $item['cart_id']; ?>" readonly>
                                        <button type="button"
                                            onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                                    </div>
                                    <input type="hidden" name="cart_id[]" value="<?php echo $item['cart_id']; ?>">
                                </td>
                                <td>$<?php echo number_format($total, 2); ?></td>
                                <td>
                                    <!-- Add a Remove Button -->
                                    <form method="POST" action="cart.php" style="display: inline;">
                                        <input type="hidden" name="remove_cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="remove-button btn-delete">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="checkout-section">
                    <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>
                    <div class="devided">
                    <form action="checkout.php">
                        <button name="checkout" type="submit" class="selected checkout-button">Checkout</button>
                    </form>
                    <form action="products.php">
                        <button name="products" type="submit" class="selected checkout-button">Return To Product</button>
                    </form>
                    </div>
                </div>

            </form>
        <?php } ?>
    </div>



    <script>
        function updateQuantity(cart_id, change) {
            var quantityInput = document.getElementById('quantity-' + cart_id);
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + change;

            if (newQuantity >= 1) {
                // Update the value in the input field
                quantityInput.value = newQuantity;

                // Send the new quantity to the server via AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_cart_quantity.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        console.log(xhr.responseText); // Optional: handle response

                        // Refresh the page to reflect the updated total
                        location.reload();
                    }
                };
                xhr.send("cart_id=" + cart_id + "&quantity=" + newQuantity);
            }
        }

    </script>

</body>

</html>