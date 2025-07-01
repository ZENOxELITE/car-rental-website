<?php
require_once 'includes/config.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT name FROM cars ORDER BY name");
    $cars = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Exact car names in database:\n";
    echo "===========================\n";
    foreach ($cars as $index => $car) {
        echo ($index + 1) . ". '$car'\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
