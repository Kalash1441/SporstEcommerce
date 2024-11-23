<?php
// Include the database connection
include('test.php'); // Replace with your database connection file

// Start session to manage the cart
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Initialize an empty cart
}

// Fetch all products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Handle Add to Cart action
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    
    // Check if the product is already in the cart
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
        $message = "Product added to cart successfully!";
    } else {
        $message = "Product is already in your cart!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
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
        .btn-success {
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Welcome to the Customer Dashboard</h1>

        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Product Listing -->
        <div class="row">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['product_description']); ?></p>
                            <p class="text-primary"><strong>$<?php echo htmlspecialchars($product['product_price']); ?></strong></p>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-success btn-sm">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Cart Navigation -->
        <div class="text-center mt-4">
            <a href="cart.php" class="btn btn-primary">Go to Cart</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>