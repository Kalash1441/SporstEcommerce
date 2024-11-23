<?php
// Start the session
session_start();

// Include the database connection
include('test.php');

// Check if vendor is logged in
if (!isset($_SESSION['vendor_logged_in']) || !$_SESSION['vendor_logged_in']) {
    header("Location: login.php");
    exit();
}

// Get vendor name and email from session
$vendor_name = $_SESSION['vendor_name'];
$vendor_email = $_SESSION['vendor_email'];

// Success and error messages
$success_message = '';
$error_message = '';

// Fetch products for the logged-in vendor
$stmt = $conn->prepare("SELECT * FROM products WHERE vendor_email = ?");
$stmt->bind_param("s", $vendor_email);
$stmt->execute();
$products = $stmt->get_result();

// Handle adding a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_description = filter_var($_POST['product_description'], FILTER_SANITIZE_STRING);

    if ($product_name && $product_price && $product_description) {
        $add_stmt = $conn->prepare("INSERT INTO products (vendor_email, product_name, product_price, product_description) VALUES (?, ?, ?, ?)");
        $add_stmt->bind_param("ssds", $vendor_email, $product_name, $product_price, $product_description);
        if ($add_stmt->execute()) {
            $success_message = "Product added successfully.";
        } else {
            $error_message = "Error adding product.";
        }
        $add_stmt->close();
    } else {
        $error_message = "Please provide valid product details.";
    }
}

// Handle deleting a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);
    $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND vendor_email = ?");
    $delete_stmt->bind_param("is", $product_id, $vendor_email);
    if ($delete_stmt->execute()) {
        $success_message = "Product deleted successfully.";
    } else {
        $error_message = "Error deleting product.";
    }
    $delete_stmt->close();
}

// Handle modifying a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modify_product'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_description = filter_var($_POST['product_description'], FILTER_SANITIZE_STRING);

    if ($product_name && $product_price && $product_description) {
        $modify_stmt = $conn->prepare("UPDATE products SET product_name = ?, product_price = ?, product_description = ? WHERE id = ? AND vendor_email = ?");
        $modify_stmt->bind_param("sdsis", $product_name, $product_price, $product_description, $product_id, $vendor_email);
        if ($modify_stmt->execute()) {
            $success_message = "Product modified successfully.";
        } else {
            $error_message = "Error modifying product.";
        }
        $modify_stmt->close();
    } else {
        $error_message = "Please provide valid product details.";
    }
}

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/vendor.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 text-primary">Welcome, <?php echo htmlspecialchars($vendor_name); ?>!</h1>
            <p class="lead">Manage your products effectively below.</p>
            <a href="index.html" class="btn btn-danger btn-sm">Logout</a>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Add Product Section -->
        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Add New Product</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Product Price</label>
                        <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_description" class="form-label">Product Description</label>
                        <textarea class="form-control" id="product_description" name="product_description" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product List Section -->
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title">Your Products</h3>
            </div>
            <div class="card-body">
                <?php if ($products->num_rows > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($product['product_price'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Modify Product -->
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                                <input type="number" name="product_price" value="<?php echo htmlspecialchars($product['product_price']); ?>" step="0.01" required>
                                                <input type="text" name="product_description" value="<?php echo htmlspecialchars($product['product_description']); ?>" required>
                                                <button type="submit" name="modify_product" class="btn btn-warning btn-sm">Modify</button>
                                            </form>
                                            <!-- Delete Product -->
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No products available. Start adding products to see them listed here!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
