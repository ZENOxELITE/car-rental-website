<?php
try {
    // First create the database
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS car_rental");
    echo "Database created successfully\n";
    
    // Connect to the car_rental database
    $pdo = new PDO("mysql:host=localhost;dbname=car_rental", "root", "");
    
    // Read and execute the SQL file
    $sql = file_get_contents('database_setup.sql');
    $pdo->exec($sql);
    
    echo "Database tables and sample data created successfully\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
