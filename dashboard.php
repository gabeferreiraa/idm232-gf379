<?php

$pageTitle = "Dashboard";

include 'header.php';

require_once './db.php'; // Include the database connection


$success = '';
$error = '';

// Handle Add Recipe
if (isset($_POST['add_recipe'])) {
    // Retrieve and sanitize form inputs
    $recipe_name = trim($_POST['recipe_name']);
    $cuisine = trim($_POST['cuisine']);
    $cook_time = trim($_POST['cook_time']);
    $servings = trim($_POST['servings']);
    $description = trim($_POST['description']);
    $dish_image = ''; // To store the path

    // Handle image upload
    if (isset($_FILES['dish_image']) && $_FILES['dish_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['dish_image']['tmp_name'];
        $fileName = $_FILES['dish_image']['name'];
        $fileSize = $_FILES['dish_image']['size'];
        $fileType = $_FILES['dish_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check allowed file extensions
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory in which the uploaded file will be moved
            $uploadFileDir = './uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $dish_image = $dest_path;
            } else {
                $error = 'There was an error moving the uploaded file.';
            }
        } else {
            $error = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    // Validate inputs
    if (empty($recipe_name) || empty($cuisine)) {
        $error = 'Please fill in all required fields (Recipe Name and Cuisine).';
    }

    if (empty($error)) {
        // Insert the recipe into the database
        $insert_stmt = $conn->prepare("INSERT INTO recipes (recipe_name, cuisine, cook_time, servings, description, dish_image) VALUES (?, ?, ?, ?, ?, ?)");
        if ($insert_stmt) {
            $insert_stmt->bind_param("ssssss", $recipe_name, $cuisine, $cook_time, $servings, $description, $dish_image);
            if ($insert_stmt->execute()) {
                $success = 'Recipe added successfully!';
            } else {
                $error = 'Error: Could not execute the query.';
            }
            $insert_stmt->close();
        } else {
            $error = 'Database error: Unable to prepare statement.';
        }
    }
}

// Handle Delete Recipe
if (isset($_GET['delete'])) {
    $recipe_id = intval($_GET['delete']);

    // Fetch the recipe details
    $fetch_stmt = $conn->prepare("SELECT dish_image FROM recipes WHERE id = ?");
    if ($fetch_stmt) {
        $fetch_stmt->bind_param("i", $recipe_id);
        $fetch_stmt->execute();
        $fetch_stmt->bind_result($existing_image);
        if ($fetch_stmt->fetch()) {
            $fetch_stmt->close();

            // Delete the recipe
            $delete_stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
            if ($delete_stmt) {
                $delete_stmt->bind_param("i", $recipe_id);
                if ($delete_stmt->execute()) {
                    // Remove the image file if exists
                    if (!empty($existing_image) && file_exists($existing_image)) {
                        unlink($existing_image);
                    }
                    $success = 'Recipe deleted successfully!';
                } else {
                    $error = 'Error: Could not execute the delete query.';
                }
                $delete_stmt->close();
            } else {
                $error = 'Database error: Unable to prepare delete statement.';
            }
        } else {
            $error = 'Recipe not found.';
            $fetch_stmt->close();
        }
    } else {
        $error = 'Database error: Unable to prepare fetch statement.';
    }
}

// Fetch all recipes
$recipes = [];
$fetch_recipes_stmt = $conn->prepare("SELECT id, recipe_name, cuisine, cook_time, servings, description, dish_image FROM recipes ");
if ($fetch_recipes_stmt) {
    $fetch_recipes_stmt->execute();
    $result = $fetch_recipes_stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    $fetch_recipes_stmt->close();
} else {
    $error = 'Database error: Unable to prepare fetch recipes statement.';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fork & Flavor - Dashboard</title>
</head>
<body>

    <main>
        <div class="container">
            <h2>Dashboard</h2>

            <?php if ($success): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <!-- Add Recipe Form -->
            <section class="add-recipe-section">
                <h3>Add New Recipe</h3>
                <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="contact-form">
                    <input type="hidden" name="add_recipe" value="1">

                    <label for="recipe_name">Recipe Name<span style="color:red;">*</span>:</label>
                    <input type="text" id="recipe_name" name="recipe_name" required>

                    <label for="cuisine">Cuisine<span style="color:red;">*</span>:</label>
                    <input type="text" id="cuisine" name="cuisine" required>

                    <label for="cook_time">Cook Time:</label>
                    <input type="text" id="cook_time" name="cook_time" placeholder="e.g., 45 minutes">

                    <label for="servings">Servings:</label>
                    <input type="text" id="servings" name="servings" placeholder="e.g., 4 servings">

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" placeholder="Add a brief description..."></textarea>

                    <label for="dish_image">Dish Image:</label>
                    <input type="file" id="dish_image" name="dish_image" accept="image/*">

                    <button type="submit" class="btn">Add Recipe</button>
                </form>
            </section>

            <!-- List of Recipes -->
            <section class="list-recipes-section">
                <h3>Your Recipes</h3>

                <?php if (count($recipes) > 0): ?>
                    <table border="1" cellpadding="10" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Recipe Name</th>
                                <th>Cuisine</th>
                                <th>Cook Time</th>
                                <th>Servings</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recipes as $recipe): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($recipe['dish_image']) && file_exists($recipe['dish_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($recipe['dish_image']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name']); ?>" width="100">
                                        <?php else: ?>
                                            <img src="default_image_path.jpg" alt="Default Image" width="100">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo utf8_decode($recipe['recipe_name']); ?></td>
                                    <td><?php echo utf8_decode($recipe['cuisine']); ?></td>
                                    <td><?php echo utf8_decode($recipe['cook_time']); ?></td>
                                    <td><?php echo utf8_decode($recipe['servings']); ?></td>
                                    <td><?php echo nl2br(utf8_decode($recipe['description'])); ?></td>
                                    <td>
                                        <a href="dashboard.php?delete=<?php echo $recipe['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You have not added any recipes yet.</p>
                <?php endif; ?>
            </section>
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