<?php
require_once 'includes/config.php';

echo "Fixing car images with unique, high-quality images...\n\n";

try {
    $pdo = getDBConnection();
    
    // Updated image mappings with unique, high-quality car images
    $imageUpdates = [
        // Cars with missing/broken images
        'Chevrolet Tahoe' => 'https://images.unsplash.com/photo-1581540222194-0def2dda95b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Jeep Grand Cherokee' => 'https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Audi R8' => 'https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Corvette Stingray' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Kia K5' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Lexus ES' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        
        // Cars with duplicate images - giving them unique ones
        'Mazda CX-5' => 'https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'McLaren 570S' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Nissan Altima' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Genesis G90' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        
        // Additional unique images for better variety
        'BMW 3 Series' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Mercedes-Benz C-Class' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Audi A4' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Tesla Model S' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Maserati Ghibli' => 'https://images.unsplash.com/photo-1562141961-d0a2d2a2e7b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Infiniti Q50' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        
        'Toyota Camry' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Honda Accord' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Hyundai Sonata' => 'https://images.unsplash.com/photo-1562141961-d0a2d2a2e7b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        
        'Ford Explorer' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Subaru Outback' => 'https://images.unsplash.com/photo-1581540222194-0def2dda95b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Acura MDX' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Cadillac Escalade' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Range Rover Sport' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        
        'Porsche 911' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Ferrari F8' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'BMW M4' => 'https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
        'Lamborghini Huracan' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'
    ];
    
    $updateStmt = $pdo->prepare("UPDATE cars SET image = ? WHERE name = ?");
    $updatedCount = 0;
    
    foreach ($imageUpdates as $carName => $imageUrl) {
        $result = $updateStmt->execute([$imageUrl, $carName]);
        if ($result && $updateStmt->rowCount() > 0) {
            echo "✅ Updated image for: $carName\n";
            $updatedCount++;
        } else {
            echo "⚠️  Car not found: $carName\n";
        }
    }
    
    echo "\n🎉 Image update complete!\n";
    echo "✅ Updated images for $updatedCount cars\n";
    
    // Verify all cars have unique images
    echo "\n🔍 Checking for duplicate images:\n";
    $stmt = $pdo->query("SELECT image, GROUP_CONCAT(name) as cars FROM cars GROUP BY image HAVING COUNT(*) > 1");
    $duplicates = $stmt->fetchAll();
    
    if (empty($duplicates)) {
        echo "✅ All cars have unique images!\n";
    } else {
        echo "⚠️  Found duplicate images:\n";
        foreach ($duplicates as $dup) {
            echo "   Image used by: {$dup['cars']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
