<?php
// Include the database connection
include('test.php');

// Variable to store success or error message
$successMessage = '';
$errorMessage = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password

    // Prepare and bind the SQL query to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO vendors (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);  // "sss" means 3 string parameters

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $successMessage = "Vendor registered successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    // Close the statement (no need to close $conn as it's managed in config.php)
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Registration</title>
    <link rel="stylesheet" href="css/1.css"> <!-- Link to your stylesheet -->
</head>
<body>
    <div class="container signup-page">
        <h2>Vendor Registration</h2>
        
        <!-- Display success or error message -->
        <?php if ($successMessage): ?>
            <div class="message success"><?php echo $successMessage; ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="message error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <!-- Sign-up form -->
        <form method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        
        <!-- Link to Login Page -->
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
