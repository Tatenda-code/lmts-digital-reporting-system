<?php

// Replace these placeholders with your real database info locally
$host     = 'YOUR_DB_HOST';
$db_name  = 'YOUR_DB_NAME';
$db_user  = 'YOUR_DB_USER';
$db_pass  = 'YOUR_DB_PASSWORD';

// Create MySQLi connection
$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
