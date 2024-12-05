<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once './db.php';

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
    <!-- Header Section -->
    <header>
        <div class="container">
            <h1><a href="index.php">Fork & Flavor</a></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="recipe.php">Recipes</a></li>
                    <li><a href="help.php">Help</a></li>
                </ul>
            </nav>
        </div>
    </header>

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

            <!-- Back Button -->
            <?php if (isset($_GET['search'])): ?>
                <section class="back-button-section">
                    <form action="index.php" method="GET">
                        <button type="submit">Back to All Recipes</button>
                    </form>
                </section>
            <?php endif; ?>

            <!-- Recipe Cards Section -->
            <section class="cards-section">
                <h2><?php echo isset($_GET['search']) ? 'Search Results' : 'Popular Recipes'; ?></h2>
                <div class="cards-container">
                    <?php
                    // Database connection
                    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Check if a search query exists
                    $searchQuery = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                    if ($searchQuery) {
                        // Fetch recipes matching the search query
                        $sql = "SELECT id, recipe_name, cuisine, cook_time, servings, dish_image 
                                FROM recipes 
                                WHERE recipe_name LIKE '%$searchQuery%' 
                                   OR cuisine LIKE '%$searchQuery%' 
                                   OR description LIKE '%$searchQuery%'";
                    } else {
                        // Fetch all recipes for the main page
                        $sql = "SELECT id, recipe_name, cuisine, cook_time, servings, dish_image FROM recipes";
                    }

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Use default image if dish_image is empty or file doesn't exist
                            $imagePath = (!empty($row['dish_image']) && file_exists($row['dish_image'])) 
                                        ? $row['dish_image'] 
                                        : 'default_image_path.jpg'; // Replace with the path to your default image
                            echo '<article class="recipe-card">';
                            echo '<a href="recipe.php?id=' . $row["id"] . '">'; // Link with recipe ID
                            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($row["recipe_name"]) . '">';
                            echo '<h3>' . htmlspecialchars($row["recipe_name"]) . '</h3>';
                            echo '</a>';
                            echo '<p>' . htmlspecialchars($row["cuisine"]) . ' | ' . $row["cook_time"] . ' min | ' . $row["servings"] . ' Servings</p>';
                            echo '</article>';
                        }
                    } else {
                        echo "<p>No recipes found.</p>";
                    }

                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2023 Fork & Flavor</p>
        </div>
    </footer>
</body>
</html>