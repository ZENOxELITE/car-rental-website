<?php
require_once 'includes/config.php';

try {
    $pdo = getDBConnection();
    
    // Create cars table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            price_per_day DECIMAL(10,2) NOT NULL,
            image VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            features TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create bookings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            car_id INTEGER NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            message TEXT,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
        )
    ");
    
    echo "Tables created successfully!\n";
    
    // Check if sample data exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM cars");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        // Insert sample cars
        $sampleCars = [
            ['BMW 3 Series', 'luxury', 89.00, 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'Experience luxury and performance with the BMW 3 Series. Perfect for business trips and special occasions.', 'Automatic Transmission,GPS Navigation,Bluetooth,Premium Sound System,Leather Seats'],
            ['Mercedes-Benz C-Class', 'luxury', 95.00, 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'Elegant and sophisticated, the Mercedes-Benz C-Class offers unmatched comfort and style.', 'Automatic Transmission,Premium Interior,Advanced Safety Features,Climate Control,Sunroof'],
            ['Toyota Camry', 'sedan', 55.00, 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'Reliable and fuel-efficient, the Toyota Camry is perfect for everyday driving and long trips.', 'Fuel Efficient,Spacious Interior,Safety Features,Bluetooth,Backup Camera'],
            ['Honda Accord', 'sedan', 58.00, 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'The Honda Accord combines reliability with modern technology for a comfortable driving experience.', 'Hybrid Option,Advanced Safety,Spacious Cabin,Infotainment System,LED Headlights'],
            ['Ford Explorer', 'suv', 75.00, 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'Spacious and powerful, the Ford Explorer is ideal for family trips and outdoor adventures.', '7-Seater,4WD Available,Towing Capacity,Advanced Tech,Cargo Space'],
            ['Chevrolet Tahoe', 'suv', 85.00, 'https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'The Chevrolet Tahoe offers maximum space and capability for large groups and heavy cargo.', '8-Seater,4WD,Premium Audio,Rear Entertainment,Massive Cargo Space'],
            ['Porsche 911', 'sports', 150.00, 'https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'Experience pure driving excitement with the iconic Porsche 911. Perfect for special occasions.', 'High Performance,Sport Suspension,Premium Interior,Advanced Electronics,Iconic Design'],
            ['Ferrari F8', 'sports', 300.00, 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80', 'The ultimate supercar experience. The Ferrari F8 delivers unmatched performance and prestige.', 'V8 Engine,Carbon Fiber,Racing Technology,Luxury Interior,Exclusive Experience']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO cars (name, category, price_per_day, image, description, features) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleCars as $car) {
            $stmt->execute($car);
        }
        
        echo "Sample car data inserted successfully!\n";
    } else {
        echo "Sample data already exists ({$result['count']} cars found)\n";
    }
    
    echo "Database setup complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
