<?php
// signup.php

// Set the page title
$pageTitle = "Sign Up";

// Include header which contains the <header> section
include 'header.php';

require_once './db.php'; // Include the database connection

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Prepare a statement to check if the email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = 'Email already exists. Please choose another.';
            } else {
                // Email is available, proceed to insert
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare an insert statement
                $insert_stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                if ($insert_stmt) {
                    $insert_stmt->bind_param("ss", $email, $hashed_password);
                    if ($insert_stmt->execute()) {
                        $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                    $insert_stmt->close();
                } else {
                    $error = 'Database error: Unable to prepare statement.';
                }
            }
            $stmt->close();
        } else {
            $error = 'Database error: Unable to prepare statement.';
        }
    }
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>

        <link rel="stylesheet" href="./index.css">
    <title>Fork & Flavor - Sign Up</title>
</head>
<body>



    <main>
        <div class="container">
            <h2>Sign Up</h2>

            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php else: ?>
                <form action="signup.php" method="POST" class="contact-form">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit" class="btn">Sign Up</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Fork & Flavor. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>