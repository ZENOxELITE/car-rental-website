<?php
require_once 'includes/config.php';
require_once 'includes/file_db.php';

echo "Testing Car Rental System\n";
echo "========================\n\n";

// Test 1: Get cars
echo "1. Testing cars retrieval:\n";
$db = new FileDB();
$cars = $db->getCars();
echo "Found " . count($cars) . " cars\n";
echo "First car: " . $cars[0]['name'] . "\n\n";

// Test 2: Get specific car
echo "2. Testing get car by ID:\n";
$car = $db->getCarById(1);
if ($car) {
    echo "Car found: " . $car['name'] . " - $" . $car['price_per_day'] . "/day\n\n";
} else {
    echo "Car not found\n\n";
}

// Test 3: Add booking
echo "3. Testing booking creation:\n";
$bookingData = [
    'car_id' => 1,
    'customer_name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'start_date' => '2024-07-01',
    'end_date' => '2024-07-05',
    'message' => 'Test booking from script'
];

$booking = $db->addBooking($bookingData);
echo "Booking created with ID: " . $booking['id'] . "\n";
echo "Customer: " . $booking['customer_name'] . "\n";
echo "Status: " . $booking['status'] . "\n\n";

// Test 4: Get all bookings
echo "4. Testing bookings retrieval:\n";
$bookings = $db->getBookings();
echo "Total bookings: " . count($bookings) . "\n";
if (count($bookings) > 0) {
    $lastBooking = end($bookings);
    echo "Last booking: " . $lastBooking['customer_name'] . " for car ID " . $lastBooking['car_id'] . "\n";
}

echo "\nAll tests completed successfully!\n";
?>
