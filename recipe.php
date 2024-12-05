<?php
// Database connection
require_once './db.php';
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css">
    <title>Recipe Details</title>
</head>
<body>
    <!-- Navigation -->
    <header class="header">
        <div class="container">
            <h1><a href="index.php" class="site-title">Fork & Flavor</a></h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="help.php">Help</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="recipe-details container">
        <?php


        // Get recipe ID from URL
        $recipeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($recipeId > 0) {
            $sql = "SELECT recipe_name, cuisine, cook_time, servings, description, ingredients, steps, dish_image, ingredients_image, steps_image 
                    FROM recipes WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $recipeId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $recipe = $result->fetch_assoc();

                // Display recipe details
                echo '<h1 class="recipe-title">' . utf8_encode($recipe['recipe_name']) . '</h1>';
                echo '<div class="recipe-image">';
                if (!empty($recipe['dish_image'])) {
                    echo '<img src="' . htmlspecialchars($recipe['dish_image']) . '" alt="' . htmlspecialchars($recipe['recipe_name']) . '" />';
                } else {
                    echo '<p>Image not available.</p>';
                }
                echo '</div>';
 
                ?>
                <div class="recipe-meta">
                    <?php 
                    echo '<p><strong>Cuisine:</strong> ' . utf8_decode($recipe['cuisine']) . '</p>';
                    echo '<p><strong>Cook Time:</strong> ' . utf8_decode($recipe['cook_time']) . ' min</p>';
                    echo '<p><strong>Servings:</strong> ' . utf8_decode($recipe['servings']) . '</p>';
                    ?>
                </div>
                <?php 
                echo '<p class="recipe-description"><strong>Description:</strong> ' . utf8_decode($recipe['description']) . '</p>';

                echo '<h2 class="section-title">Ingredients</h2>';
                echo '<img src="' . htmlspecialchars($recipe['ingredients_image']) . '" alt="' . htmlspecialchars($recipe['recipe_name']) . ' Ingredients" />';
                $ingredients = explode('*', $recipe['ingredients']); // Split by '*' delimiter
                echo '<ul class="ingredients-list">';
                foreach ($ingredients as $ingredient) {
                    if (!empty(trim($ingredient))) {
                        echo '<li>' . utf8_decode(trim($ingredient)) . '</li>';
                    }
                }
                echo '</ul>';

                echo '<h2 class="section-title">Steps</h2>';
                $steps = explode('^^', $recipe['steps']); // Split steps by '^^' delimiter
                $stepImages = explode(',', $recipe['steps_image']); // Split images by ',' delimiter
                echo '<div class="steps-container">';
                foreach ($steps as $index => $step) {
                    if (!empty(trim($step))) {
                        echo '<div class="step">';
                        echo '<p>' . nl2br(htmlspecialchars(trim($step))) . '</p>';
                        if (isset($stepImages[$index]) && !empty($stepImages[$index])) {
                            echo '<img src="' . htmlspecialchars($stepImages[$index]) . '" alt="Step ' . ($index + 1) . '" class="step-image" />';
                        }
                        echo '</div>';
                    }
                }
                echo '</div>';
            } else {
                echo '<p class="error-message">Recipe not found.</p>';
            }
            $stmt->close();
        } else {
            echo '<p class="error-message">Invalid recipe ID.</p>';
        }

        $conn->close();
        ?>
    </main>
</body>
</html>