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
                    <li><a href="recipe.php">Recipes</a></li>
                    <li><a href="help.php">Help</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="recipe-details container">
        <?php
        // Database connection
        require_once './db.php';

        $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get recipe ID from URL
        $recipeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($recipeId > 0) {
            // Fetch recipe details, including the image column
            $sql = "SELECT recipe_name, cuisine, cook_time, servings, description, ingredients, steps, dish_image, ingredients_image 
                    FROM recipes WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $recipeId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $recipe = $result->fetch_assoc();

                // Display recipe details
                echo '<h1 class="recipe-title">' . htmlspecialchars($recipe['recipe_name']) . '</h1>';
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
                    echo '<p><strong>Cuisine:</strong> ' . htmlspecialchars($recipe['cuisine']) . '</p>';
                    echo '<p><strong>Cook Time:</strong> ' . htmlspecialchars($recipe['cook_time']) . ' min</p>';
                    echo '<p><strong>Servings:</strong> ' . htmlspecialchars($recipe['servings']) . '</p>';
                    ?>
                </div>
                <?php 
                echo '<p class="recipe-description"><strong>Description:</strong> ' . htmlspecialchars($recipe['description']) . '</p>';

                // Display ingredients
                echo '<h2 class="section-title">Ingredients</h2>';
                $ingredients = explode('*', $recipe['ingredients']); // Split by '*' delimiter
                echo '<ul class="ingredients-list">';
                foreach ($ingredients as $ingredient) {
                    if (!empty(trim($ingredient))) {
                        echo '<li>' . htmlspecialchars(trim($ingredient)) . '</li>';
                    }
                }
                echo '</ul>';

                // Display steps
                echo '<h2 class="section-title">Steps</h2>';
                $steps = explode('^^', $recipe['steps']); // Split by '^^' delimiter
                echo '<ol class="steps-list">';
                foreach ($steps as $index => $step) {
                    if (!empty(trim($step))) {
                        echo '<li>' . nl2br(htmlspecialchars(trim($step))) . '</li>';
                    }
                }
                echo '</ol>';
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