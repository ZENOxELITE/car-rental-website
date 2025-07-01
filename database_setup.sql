-- Car Rental Database Setup
-- Run this script to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS car_rental;
USE car_rental;

-- Create cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    features TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create bookings table
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
);

-- Insert sample car data
INSERT INTO cars (name, category, price_per_day, image, description, features) VALUES
('BMW 3 Series', 'luxury', 89.00, 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'Experience luxury and performance with the BMW 3 Series. Perfect for business trips and special occasions.', 'Automatic Transmission,GPS Navigation,Bluetooth,Premium Sound System,Leather Seats'),

('Mercedes-Benz C-Class', 'luxury', 95.00, 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'Elegant and sophisticated, the Mercedes-Benz C-Class offers unmatched comfort and style.', 'Automatic Transmission,Premium Interior,Advanced Safety Features,Climate Control,Sunroof'),

('Toyota Camry', 'sedan', 55.00, 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'Reliable and fuel-efficient, the Toyota Camry is perfect for everyday driving and long trips.', 'Fuel Efficient,Spacious Interior,Safety Features,Bluetooth,Backup Camera'),

('Honda Accord', 'sedan', 58.00, 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'The Honda Accord combines reliability with modern technology for a comfortable driving experience.', 'Hybrid Option,Advanced Safety,Spacious Cabin,Infotainment System,LED Headlights'),

('Ford Explorer', 'suv', 75.00, 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'Spacious and powerful, the Ford Explorer is ideal for family trips and outdoor adventures.', '7-Seater,4WD Available,Towing Capacity,Advanced Tech,Cargo Space'),

('Chevrolet Tahoe', 'suv', 85.00, 'https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'The Chevrolet Tahoe offers maximum space and capability for large groups and heavy cargo.', '8-Seater,4WD,Premium Audio,Rear Entertainment,Massive Cargo Space'),

('Porsche 911', 'sports', 150.00, 'https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'Experience pure driving excitement with the iconic Porsche 911. Perfect for special occasions.', 'High Performance,Sport Suspension,Premium Interior,Advanced Electronics,Iconic Design'),

('Ferrari F8', 'sports', 300.00, 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80', 'The ultimate supercar experience. The Ferrari F8 delivers unmatched performance and prestige.', 'V8 Engine,Carbon Fiber,Racing Technology,Luxury Interior,Exclusive Experience');

-- Create indexes for better performance
CREATE INDEX idx_cars_category ON cars(category);
CREATE INDEX idx_bookings_car_id ON bookings(car_id);
CREATE INDEX idx_bookings_email ON bookings(email);
CREATE INDEX idx_bookings_dates ON bookings(start_date, end_date);
CREATE INDEX idx_bookings_status ON bookings(status);

-- Create a view for booking details with car information
CREATE VIEW booking_details AS
SELECT 
    b.id as booking_id,
    b.customer_name,
    b.email,
    b.phone,
    b.start_date,
    b.end_date,
    b.message,
    b.status,
    b.created_at as booking_date,
    c.name as car_name,
    c.category as car_category,
    c.price_per_day,
    c.image as car_image,
    DATEDIFF(b.end_date, b.start_date) as rental_days,
    (DATEDIFF(b.end_date, b.start_date) * c.price_per_day) as total_cost
FROM bookings b
JOIN cars c ON b.car_id = c.id;

-- Sample queries for testing
-- SELECT * FROM cars;
-- SELECT * FROM bookings;
-- SELECT * FROM booking_details;
-- SELECT * FROM cars WHERE category = 'luxury';
-- SELECT * FROM booking_details WHERE status = 'pending';
