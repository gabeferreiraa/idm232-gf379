<?php
// db.php - Database connection
$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$connections = new mysqli($db_server, $db_user, $db_pass, $db_name)
?>