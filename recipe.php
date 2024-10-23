
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="index.css">
    <title>Recipe List</title>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="index.php?page=home">Home</a></li>

            <li><a href="recipe.php">Recipes</a></li> <!-- Recipe page link -->
        </ul>
    </nav>

    <main class="recipes">
        <h1 class="recipes-header">Recipes</h1>
        <!-- Mock data for the recipe list -->
        <?php 
        // Temporary mock data array
        $recipes = [
            [
                'recipeName' => 'Spaghetti Bolognese',
                'ingredientName' => 'Ground Beef',
                'quantity' => '500g'
            ],
            [
                'recipeName' => 'Pancakes',
                'ingredientName' => 'Flour',
                'quantity' => '2 cups'
            ],
            [
                'recipeName' => 'Guacamole',
                'ingredientName' => 'Avocado',
                'quantity' => '3 ripe avocados'
            ]
        ];
        ?>

        <?php if ($recipes): ?>
            <ul class="recipe-list">
                <?php foreach ($recipes as $recipe): ?>
                    <li>
                        <h2><?php echo htmlspecialchars($recipe['recipeName']); ?></h2>
                        <p>Ingredient: <?php echo htmlspecialchars($recipe['ingredientName']); ?>, 
                        Quantity: <?php echo htmlspecialchars($recipe['quantity']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
    </main>

</body>
</html>