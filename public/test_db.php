<?php
$dsn = 'mysql:host=localhost;dbname=my_database;charset=utf8';
$dbUser = 'root'; // Default XAMPP MySQL user
$dbPassword = ''; // Default XAMPP MySQL password is empty

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}