<?php
// Start the session
session_start();

// Include the database connection
include('test.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the entered email and password
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM vendors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch vendor data from the database
        $vendor = $result->fetch_assoc();
        
        // Verify the entered password with the stored hashed password
        if (password_verify($password, $vendor['password'])) {
            // Set session variables for the logged-in vendor
            $_SESSION['vendor_logged_in'] = true;
            $_SESSION['vendor_email'] = $email;
            $_SESSION['vendor_name'] = $vendor['name'];

            // Redirect to vendor dashboard
            header("Location: vendor.php");
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No vendor found with that email address.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Login</title>
    <link rel="stylesheet" href="css/1.css"> <!-- Link to your stylesheet -->
</head>
<body>
    <div class="container login-page">
        <h2>Vendor Login</h2>

        <!-- Display error message if any -->
        <?php
        if (isset($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        }
        ?>

        <!-- Login Form -->
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <!-- Link to Sign Up page -->
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
