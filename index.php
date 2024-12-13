<?php 

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database connection
require_once './db.php';
include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css">
    <title>Fork & Flavor - Home</title>
</head>
<body>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Search Section -->
            <section class="search-section">
                <h2>Find Your Favorite Recipes</h2>
                <form action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search recipes..." required>
                    <button type="submit">Search</button>
                </form>
            </section>

            <?php if (isset($_GET['search'])): ?>
                <section class="back-button-section">
                    <form action="index.php" method="GET">
                        <button type="submit">Back to All Recipes</button>
                    </form>
                </section>
            <?php endif; ?>

            <section class="cards-section">
                <h2><?php echo isset($_GET['search']) ? 'Search Results' : 'Popular Recipes'; ?></h2>
                <div class="cards-container">
                    <?php

                    // Retrieve and sanitize the search query
                    $searchQuery = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

                    if ($searchQuery) {
                        // Prepare statement to prevent SQL injection
                        $stmt = $conn->prepare("SELECT id, recipe_name, cuisine, cook_time, servings, dish_image 
                                                FROM recipes 
                                                WHERE recipe_name LIKE CONCAT('%', ?, '%') 
                                                   OR cuisine LIKE CONCAT('%', ?, '%') 
                                                   OR description LIKE CONCAT('%', ?, '%')");
                        $stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
                    } else {
                        // Fetch all recipes for the main page
                        $stmt = $conn->prepare("SELECT id, recipe_name, cuisine, cook_time, servings, dish_image FROM recipes");
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Determine the image path
                            $imagePath = (!empty($row['dish_image']) && file_exists($row['dish_image'])) 
                                        ? $row['dish_image'] 
                                        : 'default_image_path.jpg'; 
                            
                            // Sanitize output with double_encode set to false
                            $recipeId = htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8', false);
                            $recipeName = htmlspecialchars($row["recipe_name"], ENT_QUOTES, 'UTF-8', false);
                            $cuisine = htmlspecialchars($row["cuisine"], ENT_QUOTES, 'UTF-8', false);
                            $cookTime = htmlspecialchars($row["cook_time"], ENT_QUOTES, 'UTF-8', false);
                            $servings = htmlspecialchars($row["servings"], ENT_QUOTES, 'UTF-8', false);
                            $imagePathEscaped = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8', false);

                            echo '<article class="recipe-card">';
                            echo '<a href="recipe.php?id=' . $recipeId . '">'; 
                            echo '<img src="' . $imagePathEscaped . '" alt="' . $recipeName . '" loading="lazy">';
                            echo '<h3>' . $recipeName . '</h3>';
                            echo '</a>';
                            echo '<p>' . $cuisine . ' | ' . $cookTime . ' | ' . $servings . '</p>';
                            echo '</article>';
                        }
                    } else {
                        echo "<p>No recipes found.</p>";
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Fork & Flavor</p>
        </div>
    </footer>
</body>
</html>