<?php
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // Proceed with login if no errors
    if (empty($errors)) {
        if ($user->login($email, $password)) {
            // Check if the logged-in user is an admin
            if ($_SESSION['is_admin']) {
                header('Location: ../admin/products.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        } else {
            $errors['general'] = "Invalid login credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            height: 100vh;
        }
        .form-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgba(237, 233, 254, 1);
            color: black !important;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -5px;
            margin-bottom: 8px;
            text-align: left;
        }
        .signup-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        .signup-link:hover {
            text-decoration: underline;
        }
        .selected {
            background-color: rgba(91, 33, 182, 1);
            border-radius: 0.5rem;
            color: white !important;
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
                    <li><a href="../views/products.php">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="../views/cart.php">Cart</a></li>
                        <li><a href="../views/logout.php">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="../views/login.php" class="selected">Login</a></li>
                        <li><a href="../views/register.php">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

    <div class="form-center">
        <div class="form-container">
            <h1>Login</h1>
            <form method="POST">
                <div class="input-container">
                    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="input-container">
                    <input type="password" name="password" placeholder="Password">
                    <?php if (isset($errors['password'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($errors['general'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errors['general']) ?></p>
                <?php endif; ?>
                
                <button type="submit" class="selected">Login</button>
            </form>
            <a href="register.php" class="signup-link">Don't have an account? Register here</a>
        </div>
    </div>

</body>
</html>
