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
    // Fetch the product by ID using the readProduct method
    $product_data = $product->readProduct($product_id);

    // If no product is found, show an error
    if (!$product_data) {
        die('Product not found.');
    }
}

// Handle the form submission for updating the product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image'];

    // Handle image upload
    if ($image['name']) {
        $uploadDir = '../assets/images/';
        $uploadFile = $uploadDir . basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            $imagePath = basename($image['name']);
        } else {
            $errorMessage = "Failed to upload image.";
        }
    } else {
        // Use the existing image if no new image is uploaded
        $imagePath = $product_data['image_url'];
    }

    // Update the product details
    if (!isset($errorMessage)) {
        $product->update($product_id, $name, $description, $price, $stock, $imagePath);
        $successMessage = "Product updated successfully!";
        header("Location: products.php");  // Redirect back to the product list
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-container">
        <header>
            <h1>Edit Product</h1>
        </header>

        <!-- Success or Error Messages -->
        <div class="messages">
            <?php if (isset($successMessage)) echo "<div class='alert success'>$successMessage</div>"; ?>
            <?php if (isset($errorMessage)) echo "<div class='alert error'>$errorMessage</div>"; ?>
        </div>

        <!-- Edit Product Form -->
        <section class="form-section">
            <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product_data['name']); ?>" required placeholder="Enter product name">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required placeholder="Enter product description"><?php echo htmlspecialchars($product_data['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product_data['price']); ?>" step="0.01" required placeholder="Enter product price">
                </div>

                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $product_data['stock']; ?>" required placeholder="Enter stock quantity">
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="../assets/images/<?php echo htmlspecialchars($product_data['image_url']); ?>" class="product-image" alt="Product Image" width="100">
                </div>

                <button type="submit" class="btn btn-submit">Update Product</button>
            </form>
        </section>
    </div>
</body>
</html>
