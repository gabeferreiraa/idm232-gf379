<?php
// Database connection
require_once './db.php';
include 'header.php';

// Create connection using mysqli
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to UTF-8
if (!$conn->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css">
    <title>Fork & Flavor - Recipe Details</title>
</head>
<body>

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

                // Display recipe details with proper encoding
                echo '<h1 class="recipe-title">' . htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8', false) . '</h1>';
                echo '<div class="recipe-image">';
                if (!empty($recipe['dish_image'])) {
                    echo '<img src="' . htmlspecialchars($recipe['dish_image'], ENT_QUOTES, 'UTF-8', false) . '" alt="' . htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8', false) . '" loading="lazy" />';
                } else {
                    echo '<p>Image not available.</p>';
                }
                echo '</div>';
     
                ?>
                <div class="recipe-meta">
                    <?php 
                    echo '<p><strong>Cuisine:</strong> ' . htmlspecialchars($recipe['cuisine'], ENT_QUOTES, 'UTF-8', false) . '</p>';
                    echo '<p><strong>Cook Time:</strong> ' . htmlspecialchars($recipe['cook_time'], ENT_QUOTES, 'UTF-8', false) . '</p>';
                    echo '<p><strong>Servings:</strong> ' . htmlspecialchars($recipe['servings'], ENT_QUOTES, 'UTF-8', false) . '</p>';
                    ?>
                </div>
                <?php 
                echo '<p class="recipe-description"><strong>Description:</strong> ' . nl2br(htmlspecialchars($recipe['description'], ENT_QUOTES, 'UTF-8', false)) . '</p>';

                echo '<h2 class="section-title">Ingredients</h2>';
                if (!empty($recipe['ingredients_image'])) {
                    echo '<img src="' . htmlspecialchars($recipe['ingredients_image'], ENT_QUOTES, 'UTF-8', false) . '" alt="' . htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8', false) . ' Ingredients" loading="lazy" />';
                }
                $ingredients = explode('*', $recipe['ingredients']); // Split by '*' delimiter
                echo '<ul class="ingredients-list">';
                foreach ($ingredients as $ingredient) {
                    $ingredient = trim($ingredient);
                    if (!empty($ingredient)) {
                        echo '<li>' . htmlspecialchars($ingredient, ENT_QUOTES, 'UTF-8', false) . '</li>';
                    }
                }
                echo '</ul>';

                echo '<h2 class="section-title">Steps</h2>';
                $steps = explode('^^', $recipe['steps']); // Split steps by '^^' delimiter
                $stepImages = explode(',', $recipe['steps_image']); // Split images by ',' delimiter
                echo '<div class="steps-container">';
                foreach ($steps as $index => $step) {
                    $step = trim($step);
                    if (!empty($step)) {
                        // Split step into title and description using '*' delimiter
                        $stepParts = explode('*', $step, 2);
                        $stepTitle = isset($stepParts[0]) ? trim($stepParts[0]) : '';
                        $stepDescription = isset($stepParts[1]) ? trim($stepParts[1]) : '';

                        echo '<div class="step">';
                        
                        if (!empty($stepTitle)) {
                            echo '<p>' . htmlspecialchars($stepTitle, ENT_QUOTES, 'UTF-8', false) . '</p>';
                        }

                        if (!empty($stepDescription)) {
                            echo '<h3 class="step-title">' . nl2br(htmlspecialchars($stepDescription, ENT_QUOTES, 'UTF-8', false)) . '</h3>';
                        }

                        if (isset($stepImages[$index]) && !empty(trim($stepImages[$index]))) {
                            echo '<img src="' . htmlspecialchars(trim($stepImages[$index]), ENT_QUOTES, 'UTF-8', false) . '" alt="Step ' . ($index + 1) . ' Image" class="step-image" loading="lazy" />';
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