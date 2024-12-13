<?php
// login.php

// Set the page title
$pageTitle = "Login";

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Prepare a statement to fetch the user
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if email exists
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();

                // Verify the password
                if (password_verify($password, $hashed_password)) {
                    // Password is correct, start a new session
                    session_regenerate_id(true); // Prevent session fixation
                    $_SESSION['user'] = $email;
                    $_SESSION['user_id'] = $id;

                    // Redirect to dashboard or protected page
                    header('Location: dashboard.php');
                    exit();
                } else {
                    // Invalid password
                    $error = 'Incorrect password.';
                }
            } else {
                // Email not found
                $error = 'Email not found.';
            }

            $stmt->close();
        } else {
            $error = 'Database error: Unable to prepare statement.';
        }
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- The <head> section is already included in header.php -->
    <title>Fork & Flavor - Login</title>
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    <!-- Main Content -->
    <main>
        <div class="container">
            <h2>Login</h2>

            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST" class="contact-form">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn">Login</button>
            </form>
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