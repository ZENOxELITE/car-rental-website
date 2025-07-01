<?php
// Test POST request to booking endpoint
$url = 'http://localhost:8000/process_booking_new.php';

$data = [
    'car_id' => 1,
    'customer_name' => 'Website Test User',
    'email' => 'website@test.com',
    'phone' => '+1234567890',
    'start_date' => '2024-07-15',
    'end_date' => '2024-07-20',
    'message' => 'Test booking from website simulation'
];

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "POST Request Test Result:\n";
echo "========================\n";
echo $result . "\n\n";

// Check if booking was stored
require_once 'includes/file_db.php';
$db = new FileDB();
$bookings = $db->getBookings();

echo "Current bookings in database:\n";
echo "============================\n";
foreach ($bookings as $booking) {
    echo "ID: {$booking['id']}, Customer: {$booking['customer_name']}, Email: {$booking['email']}\n";
}
?>
