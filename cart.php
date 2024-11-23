<?php
// Include the database connection
include('test.php'); // Replace with your database connection file

// Start the session
session_start();

// Initialize the cart session if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle item removal from the cart
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    if (($key = array_search($product_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex the array
    }
}

// Fetch products in the cart
$cart_products = [];
$total_price = 0; // Initialize total price
if (!empty($_SESSION['cart'])) {
    $cart_ids = implode(',', $_SESSION['cart']); // Convert IDs to a comma-separated string
    $query = "SELECT * FROM products WHERE id IN ($cart_ids)";
    $cart_result = $conn->query($query);
    if ($cart_result) {
        while ($product = $cart_result->fetch_assoc()) {
            $cart_products[] = $product;
            $total_price += $product['product_price']; // Sum up the total price
        }
    }
}

// Handle Payment (Mock Implementation for Now)
if (isset($_POST['proceed_payment'])) {
    $payment_method = $_POST['payment_method'];
    $message = "Payment successful using " . htmlspecialchars($payment_method) . ". Total paid: $" . number_format($total_price, 2);
    $_SESSION['cart'] = []; // Clear the cart after successful payment
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary, .btn-danger, .btn-success {
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger:hover {
            background-color: #bd2130;
        }
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Your Cart</h1>

        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-success text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Cart Items -->
        <?php if (!empty($cart_products)): ?>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                                <td>1</td> <!-- Adjust if quantity management is implemented -->
                                <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Display Total -->
            <div class="text-end mb-4">
                <h4>Total Price: $<?php echo number_format($total_price, 2); ?></h4>
            </div>

            <!-- Payment Options -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Payment Options</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Select Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="" disabled selected>Choose...</option>
                                <option value="Credit/Debit Card">Credit/Debit Card</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Cash on Delivery">Cash on Delivery</option>
                            </select>
                        </div>
                        <button type="submit" name="proceed_payment" class="btn btn-success">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Your cart is empty. Start shopping now!</div>
        <?php endif; ?>

        <!-- Back to Portal Button -->
        <div class="text-center">
            <a href="index.html" class="btn btn-primary">Return to eCommerce Portal</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
