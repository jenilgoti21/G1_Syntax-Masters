<?php

session_start();
require('../fpdf/fpdf.php'); // Include FPDF library
require_once '../classes/Cart.php';
require_once '../classes/Database.php';
require_once '../classes/Checkout.php';


// Validation functions
function is_text_only($input_value)
{
    return preg_match("/^[a-zA-Z-' ]*$/", $input_value);
}

function is_email($input_value)
{
    return filter_var($input_value, FILTER_VALIDATE_EMAIL);
}

function is_valid_phone($input_value)
{
    return preg_match("/^\d{10}$/", $input_value);
}

function is_valid_zip($input_value)
{
    return preg_match("/^[a-zA-Z0-9]{6}$/", $input_value);
}

function is_valid_card_number($input_value)
{
    return preg_match("/^\d{16}$/", $input_value);
}

function is_valid_expiry($input_value)
{
    return preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $input_value);
}

function is_valid_cvv($input_value)
{
    return preg_match("/^\d{3}$/", $input_value);
}


// Fetch cart items before handling form submission
$cart = new Cart();
$user_id = $_SESSION['user_id'];
$cart_items = $cart->getCartItems($user_id);

// Calculate totals
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$tax = $total_price * 0.1; // 10% tax
$grand_total = $total_price + $tax;





// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = []; // Reset errors array for each form submission

    // Validate First Name
    if (empty($_POST['first_name'])) {
        $errors[] = "<p>Error!! First name cannot be empty.</p>";
    } else {
        $first_name = $_POST['first_name'];
        if (!is_text_only($first_name)) {
            $errors[] = "<p>Error!! First name should contain text only.</p>";
        }
    }

    // Validate Last Name
    if (empty($_POST['last_name'])) {
        $errors[] = "<p>Error!! Last name cannot be empty.</p>";
    } else {
        $last_name = $_POST['last_name'];
        if (!is_text_only($last_name)) {
            $errors[] = "<p>Error!! Last name should contain text only.</p>";
        }
    }

    // Validate Email
    if (empty($_POST['email'])) {
        $errors[] = "<p>Error!! Email cannot be empty.</p>";
    } else {
        $email = $_POST['email'];
        if (!is_email($email)) {
            $errors[] = "<p>Error!! Invalid Email.</p>";
        }
    }

    // Validate Mobile Number
    if (empty($_POST['mobile_no'])) {
        $errors[] = "<p>Error!! Mobile number cannot be empty.</p>";
    } else {
        $mobile_no = $_POST['mobile_no'];
        if (!is_valid_phone($mobile_no)) {
            $errors[] = "<p>Error!! Mobile number must be 10 digits.</p>";
        }
    }

    // Validate Address
    if (empty($_POST['address'])) {
        $errors[] = "<p>Error!! Address cannot be empty.</p>";
    }

    // Validate City
    if (empty($_POST['city'])) {
        $errors[] = "<p>Error!! City cannot be empty.</p>";
    }

    // Validate State
    if (empty($_POST['state'])) {
        $errors[] = "<p>Error!! State cannot be empty.</p>";
    }

    // Validate Zip Code
    if (empty($_POST['zip_code'])) {
        $errors[] = "<p>Error!! Zip code cannot be empty.</p>";
    } else {
        $zip_code = $_POST['zip_code'];
        if (!is_valid_zip($zip_code)) {
            $errors[] = "<p>Error!! Zip code must be 5 digits.</p>";
        }
    }

    // Validate Payment Method
    if (empty($_POST['payment_method'])) {
        $errors[] = "<p>Error!! Payment method must be selected.</p>";
    } else {
        $payment_method = $_POST['payment_method'];

        // If Credit Card is selected, validate card details
        if ($payment_method == 'credit_card') {
            if (empty($_POST['card_holder_name'])) {
                $errors[] = "<p>Error!! Card holder name cannot be empty.</p>";
            } else {
                $card_holder_name = $_POST['card_holder_name'];
                if (!is_text_only($card_holder_name)) {
                    $errors[] = "<p>Error!! Card holder name should contain text only.</p>";
                }
            }

            if (empty($_POST['card_number'])) {
                $errors[] = "<p>Error!! Card number cannot be empty.</p>";
            } else {
                $card_number = $_POST['card_number'];
                if (!is_valid_card_number($card_number)) {
                    $errors[] = "<p>Error!! Card number must be 16 digits.</p>";
                }
            }

            if (empty($_POST['expiry_date'])) {
                $errors[] = "<p>Error!! Expiry date cannot be empty.</p>";
            } else {
                $expiry_date = $_POST['expiry_date'];
                if (!is_valid_expiry($expiry_date)) {
                    $errors[] = "<p>Error!! Expiry date must be in MM/YY format.</p>";
                }
            }

            if (empty($_POST['cvv'])) {
                $errors[] = "<p>Error!! CVV cannot be empty.</p>";
            } else {
                $cvv = $_POST['cvv'];
                if (!is_valid_cvv($cvv)) {
                    $errors[] = "<p>Error!! CVV must be 3 digits.</p>";
                }
            }
        }
    }

    // If no errors, proceed to PDF generation
    if (count($errors) == 0) {
        // Gather form details
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $mobile_no = $_POST['mobile_no'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $payment_method = $_POST['payment_method'];

        $card_details = '';
        if ($payment_method == 'credit_card') {
            $card_number = $_POST['card_number'];
            $last_four_digits = substr($card_number, -4);
            $card_details = "Payment by Credit Card XXXX XXXX XXXX $last_four_digits";
        } else {
            $card_details = "Payment by Cash on Delivery";
        }


        // 1. Create Order
        $order = new Order();
        $order_id = $order->createOrder($user_id, $grand_total);

        // 2. Create Checkout Information
        $checkout_result = $order->createCheckout(
            $order_id,
            $user_id,
            $first_name,
            $last_name,
            $email,
            $mobile_no,
            $address,
            $city,
            $state,
            $zip_code,
            $payment_method,
            isset($card_holder_name) ? $card_holder_name : '',
            isset($card_number) ? $card_number : '',
            isset($expiry_date) ? $expiry_date : '',
            isset($cvv) ? $cvv : '',
            $total_price,
            $tax,
            $grand_total
        );

        // 3. Create Order Details
        $order->createOrderDetails($order_id, $cart_items);


        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add invoice details
        $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
        // $pdf->Cell(0, 10, 'Microwave Oven Store', 0, 1, 'C');
        $imagePath = '../assets/images/logo.png'; 
        $pdf->Image($imagePath, 80, 20, 50); 
        $pdf->Ln(60);
        $pdf->SetFont('Arial', '', size: 12);
        $pdf->Cell(0, 10, "Customer: $first_name $last_name", 0, 1);
        $pdf->Cell(0, 10, "Email: $email", 0, 1);
        $pdf->Cell(0, 10, "Mobile: $mobile_no", 0, 1);
        $pdf->Cell(0, 10, "Address: $address, $city, $state, $zip_code", 0, 1);
        $pdf->Cell(0, 10, $card_details, 0, 1);
        $pdf->Ln(10);

        // Add product details
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Product Name', 1);
        $pdf->Cell(40, 10, 'Price', 1);
        $pdf->Cell(30, 10, 'Quantity', 1);
        $pdf->Cell(40, 10, 'Total', 1, 1);
        $pdf->SetFont('Arial', '', 12);

        foreach ($cart_items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $pdf->Cell(80, 10, $item['name'], 1);
            $pdf->Cell(40, 10, '$' . number_format($item['price'], 2), 1);
            $pdf->Cell(30, 10, $item['quantity'], 1);
            $pdf->Cell(40, 10, '$' . number_format($item_total, 2), 1, 1);
        }

        // Add totals
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Subtotal: $' . number_format($total_price, 2), 0, 1, );
        $pdf->Cell(0, 10, 'Tax (10%): $' . number_format($tax, 2), 0, 1);
        $pdf->Cell(0, 10, 'Grand Total: $' . number_format($grand_total, 2), 0, 1);

        // Output PDF
        ob_end_clean(); // Clear the output buffer
        $pdf->Output('F', '../downloads/Invoice_' . $order_id . '.pdf'); // Force download

        // Clear cart after order
        $cart->clearCart($user_id);

        // Redirect to thank you page
        header('Location: thank_you.php?order_id=' . $order_id);
        exit;
    } else {
        // Show errors if validation failed
        foreach ($errors as $error) {
            echo $error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Checkout</title>
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            padding: 20px 40px;
            max-width: 600px;
            width: 100%;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .subtitle {
            color: green;
            margin-bottom: 20px;
            text-align: center;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .input-container input,
        .input-container select,
        .input-container textarea {
            width: 90%;
            padding: 14px 16px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
            outline: none;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .input-container input:focus,
        .input-container select:focus,
        .input-container textarea:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.1);
        }

        .placeholder {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #aaa;
            pointer-events: none;
            transition: all 0.3s;
        }

        .input-container input:focus~.placeholder,
        .input-container input:not(:placeholder-shown)~.placeholder,
        .input-container select:focus~.placeholder,
        .input-container select:not([value=""])~.placeholder,
        .input-container textarea:focus~.placeholder,
        .input-container textarea:not(:placeholder-shown)~.placeholder {
            top: -12px;
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }

        .submit {
            background: #4f46e5;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .submit:hover {
            background: #3f3cc6;
        }

        #credit_card_details {
            display: none;
        }

        .span_line {
            display: block;
            width: 100%;
            height: 3px;
            background: #4CAF50;
            margin: 10px 0px 40px;
        }

        .input-width {
            width: 100% !important;
        }

        h2 {
            margin-bottom: 20px;
            color: green;
            text-align: center;
        }

        .error {
            color: red !important;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>


    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- Checkout Form -->
        <div style="flex: 1;">

            <form action="./checkout.php" method="POST" class="form">

                <h2 class="subtitle">Billing Information</h2>
                <span class="span_line"></span>


                <div class="input-container">
                    <input type="text" name="first_name" class="input" id="first_name">
                    <label for="first_name" class="placeholder">First Name</label>
                </div>

                <div class="input-container">
                    <input type="text" name="last_name" class="input" id="last_name">
                    <label for="last_name" class="placeholder">Last Name</label>
                </div>

                <div class="input-container">
                    <input type="email" name="email" class="input" id="email">
                    <label for="email" class="placeholder">Email</label>
                </div>

                <div class="input-container">
                    <input type="text" name="mobile_no" class="input" id="mobile_no">
                    <label for="mobile_no" class="placeholder">Mobile Number</label>
                </div>

                <div class="input-container">
                    <textarea name="address" class="input" id="address"></textarea>
                    <label for="address" class="placeholder">Address</label>
                </div>

                <div class="input-container">
                    <input type="text" name="city" class="input" id="city">
                    <label for="city" class="placeholder">City</label>
                </div>

                <div class="input-container">
                    <input type="text" name="state" class="input" id="state">
                    <label for="state" class="placeholder">State</label>
                </div>

                <div class="input-container">
                    <input type="text" name="zip_code" class="input" id="zip_code">
                    <label for="zip_code" class="placeholder">Zip Code</label>
                </div>

                <h2 class="subtitle">Payment Information</h2>
                <span class="span_line"></span>

                <div class="input-container">
                    <select name="payment_method" class="input input-width" id="payment_method">
                        <option value="">Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="cash_on_delivery">Cash on Delivery</option>
                    </select>
                    <label for="payment_method" class="placeholder">Payment Method</label>
                </div>

                <div id="credit_card_details">
                    <div class="input-container">
                        <input type="text" name="card_holder_name" class="input" id="card_holder_name">
                        <label for="card_holder_name" class="placeholder">Card Holder Name</label>
                    </div>

                    <div class="input-container">
                        <input type="text" name="card_number" class="input" id="card_number">
                        <label for="card_number" class="placeholder">Card Number</label>
                    </div>

                    <div class="input-container">
                        <input type="text" name="expiry_date" class="input" id="expiry_date">
                        <label for="expiry_date" class="placeholder">Expiry Date (MM/YY)</label>
                    </div>

                    <div class="input-container">
                        <input type="text" name="cvv" class="input" id="cvv">
                        <label for="cvv" class="placeholder">CVV</label>
                    </div>
                </div>

                <button class="submit" type="submit">Place Order</button>
                <a href="cart.php" class="submit"
                    style="background-color: #f44336; text-decoration: none; display: block; text-align: center; padding: 14px 20px; border-radius: 8px; color: #fff; margin-top: 10px; width: 90%;">Return
                    to Cart</a>

            </form>

        </div>

        <div style="flex: 1; background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin-left: 50px;">
            <h2>Order Summary</h2>
            <span class="span_line"></span>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 8px; border-bottom: 2px solid #ddd;">Item</th>
                        <th style="text-align: center; padding: 8px; border-bottom: 2px solid #ddd;">Price</th>
                        <th style="text-align: center; padding: 8px; border-bottom: 2px solid #ddd;">Quantity</th>
                        <th style="text-align: right; padding: 8px; border-bottom: 2px solid #ddd;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #ddd;">
                                $<?php echo number_format($item['price'], 2); ?></td>
                            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #ddd;">
                                <?php echo $item['quantity']; ?>
                            </td>
                            <td style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <hr>
            <table style="width: 100%; margin-top: 10px;">
                <tr>
                    <td style="text-align: right; padding: 8px;">Subtotal:</td>
                    <td style="text-align: right; padding: 8px;">$<?php echo number_format($total_price, 2); ?></td>
                </tr>
                <tr>
                    <td style="text-align: right; padding: 8px;">Tax (10%):</td>
                    <td style="text-align: right; padding: 8px;">$<?php echo number_format($tax, 2); ?></td>
                </tr>
                <tr>
                    <td style="text-align: right; font-weight: bold; padding: 8px;">Total:</td>
                    <td style="text-align: right; font-weight: bold; padding: 8px;">
                        $<?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </table>
        </div>

    </div>

    <script>
        // Show/Hide Credit Card Fields Based on Payment Method
        const paymentMethodSelect = document.getElementById('payment_method');
        const creditCardDetails = document.getElementById('credit_card_details');

        paymentMethodSelect.addEventListener('change', function () {
            if (this.value === 'credit_card') {
                creditCardDetails.style.display = 'block';
            } else {
                creditCardDetails.style.display = 'none';
            }
        });


        document.querySelector('form').addEventListener('submit', function () {
            // Optionally, show a confirmation message or redirect
            setTimeout(function () {
                document.querySelector('form').reset(); // Clear form fields
            }, 1000); // Add a slight delay before clearing the form to allow for server-side processing
        });
    </script>

</body>

</html>