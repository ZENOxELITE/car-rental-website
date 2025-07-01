<?php
/**
 * Car Rental Website Setup Script
 * Run this script once to initialize the database and verify configuration
 */

// Include configuration
require_once 'includes/config.php';

// HTML output for setup page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #f0fff0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #fff0f0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #f0f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .step { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>🚗 Elite Car Rentals - Setup</h1>
    
    <?php
    $setupComplete = false;
    
    if (isset($_POST['setup'])) {
        echo "<h2>Running Setup...</h2>";
        
        // Test database connection
        echo "<div class='step'>";
        echo "<h3>Step 1: Testing Database Connection</h3>";
        try {
            $pdo = getDBConnection();
            echo "<div class='success'>✅ Database connection successful!</div>";
            
            // Create tables
            echo "<h3>Step 2: Creating Database Tables</h3>";
            
            // Create cars table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS cars (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    category VARCHAR(50) NOT NULL,
                    price_per_day DECIMAL(10,2) NOT NULL,
                    image VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    features TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create bookings table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS bookings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    car_id INT NOT NULL,
                    customer_name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    phone VARCHAR(20) NOT NULL,
                    start_date DATE NOT NULL,
                    end_date DATE NOT NULL,
                    message TEXT,
                    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
                )
            ");
            
            echo "<div class='success'>✅ Database tables created successfully!</div>";
            
            // Check if sample data exists
            echo "<h3>Step 3: Inserting Sample Data</h3>";
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
                
                echo "<div class='success'>✅ Sample car data inserted successfully!</div>";
            } else {
                echo "<div class='info'>ℹ️ Sample data already exists ({$result['count']} cars found)</div>";
            }
            
            // Test email configuration
            echo "<h3>Step 4: Testing Email Configuration</h3>";
            if (function_exists('mail')) {
                echo "<div class='success'>✅ PHP mail function is available</div>";
                echo "<div class='info'>ℹ️ Email notifications will work if your server is configured for mail sending</div>";
            } else {
                echo "<div class='error'>⚠️ PHP mail function is not available. Email notifications will not work.</div>";
            }
            
            // File permissions check
            echo "<h3>Step 5: Checking File Permissions</h3>";
            if (is_writable('.')) {
                echo "<div class='success'>✅ Directory is writable for error logging</div>";
            } else {
                echo "<div class='error'>⚠️ Directory is not writable. Error logging may not work properly.</div>";
            }
            
            $setupComplete = true;
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
            echo "<div class='info'>Please check your database configuration in includes/config.php</div>";
        }
        echo "</div>";
    }
    
    if (!$setupComplete) {
    ?>
    
    <div class="info">
        <h3>Before running setup, please ensure:</h3>
        <ul>
            <li>MySQL database server is running</li>
            <li>Database credentials are configured in <code>includes/config.php</code></li>
            <li>PHP has necessary permissions</li>
        </ul>
    </div>
    
    <form method="post">
        <button type="submit" name="setup">Run Setup</button>
    </form>
    
    <?php } else { ?>
    
    <div class="success">
        <h2>🎉 Setup Complete!</h2>
        <p>Your car rental website is now ready to use.</p>
        <p><strong>Next steps:</strong></p>
        <ul>
            <li><a href="index.html">Visit your website</a></li>
            <li>Test the booking functionality</li>
            <li>Customize the design and content as needed</li>
            <li>Configure email settings for production use</li>
        </ul>
    </div>
    
    <div class="info">
        <h3>Important Security Notes:</h3>
        <ul>
            <li>Delete this setup.php file after setup is complete</li>
            <li>Change default database passwords</li>
            <li>Configure proper email authentication for production</li>
            <li>Set up SSL certificate for secure data transmission</li>
        </ul>
    </div>
    
    <?php } ?>
    
</body>
</html>
