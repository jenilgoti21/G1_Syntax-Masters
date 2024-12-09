<?php
require_once '../classes/User.php';
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $error = [];

    // Validate name
    if (empty($name)) {
        $error['name'] = "Full Name is required.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Invalid email format.";
    }

    if (empty($email)) {
        $error['email'] = "Email is required.";
    }

    // Validate password strength
    if (empty($password)) {
        $error['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $error['password'] = "Password must be at least 6 characters long.";
    }

    // Validate phone number (simple numeric check, adjust as needed)
    if (empty($phone_number) || !preg_match('/^\+?[0-9]{10,15}$/', $phone_number)) {
        $error['phone_number'] = "Please enter a valid phone number.";
    }

    // Validate address
    if (empty($address)) {
        $error['address'] = "Address is required.";
    }

    // Proceed only if no validation errors
    if (empty($error)) {
        // Use Database class directly to get the connection
        $database = new Database();
        $conn = $database->getConnection();  // Get the PDO connection

        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $error['email'] = "Email is already registered.";
        } else {
            // Proceed with registration
            if ($user->register($name, $email, $password, $phone_number, $address)) {
                header('Location: login.php');
                exit;
            } else {
                $error['general'] = "Failed to register. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
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

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
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
            font-size: 14px;
            margin-top: 10px;
        }

        .login-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .selected {
            background-color: rgba(91, 33, 182, 1);
            border-radius: 0.5rem;
            color: white !important;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -5px;
            margin-bottom: 8px;
            text-align: left;
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
                        <li><a href="../views/login.php">Login</a></li>
                        <li><a href="../views/register.php" class="selected">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>


    <div class="form-center">
        <div class="form-container">
            <h1>Register</h1>
            <form method="POST">
                <div class="input-container">
                    <input type="text" name="name" placeholder="Full Name" value="<?php echo isset($name) ? $name : ''; ?>">
                    <?php if (isset($error['name'])): ?>
                        <p class="error-message"><?php echo $error['name']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="input-container">
                    <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
                    <?php if (isset($error['email'])): ?>
                        <p class="error-message"><?php echo $error['email']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="input-container">
                    <input type="password" name="password" placeholder="Password">
                    <?php if (isset($error['password'])): ?>
                        <p class="error-message"><?php echo $error['password']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="input-container">
                    <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo isset($phone_number) ? $phone_number : ''; ?>">
                    <?php if (isset($error['phone_number'])): ?>
                        <p class="error-message"><?php echo $error['phone_number']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="input-container">
                    <textarea name="address" placeholder="Address"><?php echo isset($address) ? $address : ''; ?></textarea>
                    <?php if (isset($error['address'])): ?>
                        <p class="error-message"><?php echo $error['address']; ?></p>
                    <?php endif; ?>
                </div>

                <?php if (isset($error['general'])): ?>
                    <p class="error-message"><?php echo $error['general']; ?></p>
                <?php endif; ?>
                
                <button type="submit" class="selected">Register</button>
            </form>
            <a href="login.php" class="login-link">Already have an account? Login here</a>
        </div>
    </div>

</body>

</html>