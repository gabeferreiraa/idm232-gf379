<?php
// header.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Flavor - <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Home'; ?></title>
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <h1><a href="index.php">Fork & Flavor</a></h1>
            <nav>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Signup</a></li>
                    <?php endif; ?>
                    <li class="help-link"><a href="help.php">Help</a></li>
                </ul>
            </nav>
        </div>
    </header>