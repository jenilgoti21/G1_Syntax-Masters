<?php
session_start();
require_once '../classes/Product.php';


// Check if the 'order' GET parameter is set. If not, use default 'ASC'
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$product = new Product();
$products = $product->readAll($order);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="navbar-container">
                <img src="../assets/images/logo.png" alt="logo" class="navbar-logo">
                <ul class="navbar-links">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="products.php" class="selected">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

    <div class="main-container">

        <div class="centre">
            <h1>Our Microwave Ovens</h1>
        </div>

        <!-- Search Filter -->
        <section class="filter-section">
            <input type="text" id="filterInput" placeholder="Filter by name..." onkeyup="filterProducts()">

            <select id="sortFilter" onchange="sortProducts()">
                <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>
        </section>

        <!-- Product Grid -->
        <section class="product-grid" id="productList">
            <?php foreach ($products as $prod) { ?>
                <div class="product-item">
                    <!-- Product Image -->
                    <div class="item_container">
                        <?php if (!empty($prod['image_url'])) { ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($prod['image_url']); ?>"
                                class="product-image" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                        <?php } else { ?>
                            <p>No image available</p>
                        <?php } ?>

                    </div>

                    <div>

                        <h2 class="product-name"><?php echo htmlspecialchars($prod['name']); ?></h2>
                        <p class="product-description"><?php echo htmlspecialchars($prod['description']); ?></p>
                        <p class="product-price">Price: $<?php echo number_format($prod['price'], 2); ?></p>
                        <button onclick="addToCart(<?php echo $prod['product_id']; ?>)" class="btn-add-to-cart"
                            data-loggedin="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">Add to
                            Cart</button>
                        <div id="toast-container"
                            style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1000;">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>

    <script>
        // Filter products by name dynamically
        function filterProducts() {
            var input = document.getElementById('filterInput');
            var filter = input.value.toLowerCase();
            var ul = document.getElementById('productList');
            var li = ul.getElementsByClassName('product-item');

            for (var i = 0; i < li.length; i++) {
                var name = li[i].getElementsByClassName('product-name')[0];
                if (name) {
                    var txtValue = name.textContent || name.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        li[i].style.display = '';
                    } else {
                        li[i].style.display = 'none';
                    }
                }
            }
        }


        function showToast(message, color = '#28a745') {
            // Create toast element
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
        background-color: ${color};
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        margin-bottom: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        animation: fadeout 3s forwards;
    `;

            // Append toast to container
            const container = document.getElementById('toast-container');
            container.appendChild(toast);

            // Remove toast after animation ends
            setTimeout(() => {
                container.removeChild(toast);
            }, 3000);
        }

        // function addToCart(productId) {
        //     const xhr = new XMLHttpRequest();
        //     xhr.open("GET", `cart.php?action=add&product_id=${productId}`, true);
        //     xhr.onreadystatechange = function () {
        //         if (xhr.readyState === 4 && xhr.status === 200) {
        //             showToast('Product added to cart successfully!', '#28a745');
        //         }
        //     };
        //     xhr.send();
        // }

        function addToCart(productId) {
            const button = event.target;
            const isLoggedIn = button.getAttribute('data-loggedin') === 'true';

            if (!isLoggedIn) {
                // If not logged in, redirect to the login page
                window.location.href = 'login.php';
                return;
            }

            // If logged in, proceed with adding the product to the cart
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `cart.php?action=add&product_id=${productId}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    showToast('Product added to cart successfully!', '#28a745');
                }
            };
            xhr.send();
        }


        // Fadeout animation for the toast
        const style = document.createElement('style');
        style.innerHTML = `
    @keyframes fadeout {
        0% { opacity: 1; }
        100% { opacity: 0; transform: translateY(-20px); }
    }
`;
        document.head.appendChild(style);


        function sortProducts() {
            const selectedValue = document.getElementById('sortFilter').value;
            const url = new URL(window.location.href);
            if (selectedValue === "NONE") {
                url.searchParams.delete('order'); // Remove sorting parameter
            } else {
                url.searchParams.set('order', selectedValue); // Set sorting parameter
            }
            window.location.href = url.toString(); // Reload with new parameter
        }

    </script>
</body>

</html>