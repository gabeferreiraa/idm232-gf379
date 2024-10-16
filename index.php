<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="/css/normalize.css"> -->
    <link rel="stylesheet" href="index.css">
    <title>My PHP Site</title>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="index.php?page=home">Home</a></li>
            <li><a href="./recipe.php">Recipe</a></li>

        </ul>
    </nav>

    <main>
        <?php
        // Basic routing based on 'page' parameter
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // Switch case to handle different pages
            switch ($page) {
                case "home":
                    echo "<h1>Welcome to the Home Page</h1>";
                    break;
                case "page1":
                    echo "<h1>This is Page 1</h1>";
                    break;
                case "page2":
                    echo "<h1>This is Page 2</h1>";
                    break;
                case "page3":
                    echo "<h1>This is Page 3</h1>";
                    break;
                default:
                    echo "<h1>404 Page Not Found</h1>";
                    break;
            }
        } else {
            // Default to Home page
            echo "<h1>Welcome to the Home Page</h1>";
        }
        ?>

        <!-- Calculator Form (kept from your original file) -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="number" name="num01" placeholder="Enter Number">
            <Label>Enter a number to be calculated</Label>
            <select name="operator">
                <option value="add">+</option>
                <option value="subtract">-</option>
                <option value="multiply">*</option>
                <option value="divide">÷</option>
            </select>
            <input type="number" name="num02" placeholder="Enter another number">
            <button type="submit">Calculate</button>
        </form>

        <?php
        // Calculator Logic (kept from your original file)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $num01 = filter_input(INPUT_POST, "num01", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $num02 = filter_input(INPUT_POST, "num02", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $operator = htmlspecialchars($_POST["operator"]);

            $errors = false;

            if (empty($num01) || empty($num02) || empty($operator)) {
                echo "<p class='calc-error'>Fill in all fields!</p>";
                $errors = true;
            }
            if (!is_numeric($num01) || !is_numeric($num02)) {
                echo "<p class='calc-error'>ENTER NUMBERS ONLY!</p>";
                $errors = true;
            }

            if (!$errors) {
                $value = null;
                switch ($operator) {
                    case "add":
                        $value = $num01 + $num02;
                        break;
                    case "subtract":
                        $value = $num01 - $num02;
                        break;
                    case "multiply":
                        $value = $num01 * $num02;
                        break;
                    case "divide":
                        if ($num02 == 0) {
                            echo "<p class='calc-error'>Cannot divide by zero!</p>";
                            $errors = true;
                        } else {
                            $value = $num01 / $num02;
                        }
                        break;
                    default:
                        echo "<p class='calc-error'>Something went wrong!</p>";
                }

                if (!$errors) {
                    echo "<p class='calc-answer'>Result = " . $value . "</p>";
                }
            }
        }
        ?>
    </main>
</body