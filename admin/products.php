<?php
require_once '../classes/Product.php';
session_start();

// Check if the admin is logged in (You can add authentication for admin users here).
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../views/login.php');
    exit();
}

$product = new Product();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new product
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image'];

    // Handle image upload
    $uploadDir = '../assets/images/';
    $uploadFile = $uploadDir . basename($image['name']);

    if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
        $imagePath = basename($image['name']);
        $product->createProduct($name, $description, $price, $stock, $imagePath);
        $successMessage = "Product added successfully!";
    } else {
        $errorMessage = "Failed to upload image.";
    }
}

// Fetch all products
$products = $product->readAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-container">

            <header>
                <h1>Admin Panel - Manage Products</h1>
                <nav class="navbar">
            <div class="navbar-container">
            <img src="../assets/images/logo.png" alt="logo" class="navbar-logo">
                <ul class="navbar-links">
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="../views/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="../views/login.php">Login</a></li>
                    <li><a href="../views/register.php">Register</a></li>
                <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

        <!-- Success or Error Messages -->
        <div class="messages">
            <?php if (isset($successMessage)) echo "<div class='alert success'>$successMessage</div>"; ?>
            <?php if (isset($errorMessage)) echo "<div class='alert error'>$errorMessage</div>"; ?>
        </div>

        <!-- Add Product Form -->
        <section class="form-section">
            <h2>Add New Product</h2>
            <form action="products.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter product name">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required placeholder="Enter product description"></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required placeholder="Enter product price">
                </div>

                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required placeholder="Enter stock quantity">
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-submit">Add Product</button>
            </form>
        </section>

        <!-- Product List -->
        <section class="product-list-section">
            <h2>Existing Products</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod) { ?>
                        <tr class="product-row">
                            <td><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['description']); ?></td>
                            <td>$<?php echo number_format($prod['price'], 2); ?></td>
                            <td><?php echo $prod['stock']; ?></td>
                            <td>
                                <?php if (!empty($prod['image_url'])) { ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($prod['image_url']); ?>" class="product-image" alt="Product Image">
                                <?php } else { ?>
                                    <span>No image available</span>
                                <?php } ?>
                            </td>
                            <td class="display-flex">
                                <a href="edit_product.php?id=<?php echo $prod['product_id']; ?>" class="btn btn-action btn-space">Edit</a>
                                <a href="delete_product.php?id=<?php echo $prod['product_id']; ?>" class="btn btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
