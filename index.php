<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/normalize.css">
    <link rel="stylesheet" href="/css/index.css">
    <title>My PHP Site</title>
</head>
<body>
    <main>
        <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="number" name="num01" placeholder="Enter Number">
            <Label>Enter a number to be calculated </Label>
            <select name="operator" >
                <option value="add">+</option>
                <option value="subtract">-</option>
                <option value="multiply">*</option>
                <option value="divide">÷</option>
            </select>
            <input type="number" name="num02" placeholder="Enter another number">
            <button>Calculate</button>
        </form>
    </main>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $num01 = filter_input(INPUT_POST, "num01", FILTER_SANITIZE_NUMBER_FLOAT);
        $num02 = filter_input(INPUT_POST, "num02", FILTER_SANITIZE_NUMBER_FLOAT);
        $operator = htmlspecialchars($_POST["operator"]);

        //Error Handlers
        $errors = false;

        if (empty($num01) || empty($num02) || empty($operator)) {
            echo "<p class='calc-error'>Fill in all fields!</p>";
            $errors = true;
        }
        if (!is_numeric($num01) || !is_numeric($num02)) {
            echo "<p class='calc-error'>ENTER NUMBERS ONLY!</p>";
            $errors = true;
        }

        //Calculate the numbers if there are no errors
        if (!$errors) {
            $value;
            switch ($operator) {
                case "add":
                    $value = $num01 + $num02;
                    break;
                case "subract":
                    $value = $num01 - $num02;
                    break;
                case "multiply":
                    $value = $num01 * $num02;
                    break;
                case "divide":
                    $value = $num01 / $num02;
                        break;
                default: echo "<p class='calc-error'>Something went wrong!</p>";
            }

            echo "<p class='calc-answer'>Result = " . 
            $value .  "</p>";
            is_n
        }
    }
    ?>
</body>
</html>