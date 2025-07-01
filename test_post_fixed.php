<?php
// Test POST request to booking endpoint with future dates
$url = 'http://localhost:8000/process_booking_new.php';

// Use future dates
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$nextWeek = date('Y-m-d', strtotime('+7 days'));

$data = [
    'car_id' => 1,
    'customer_name' => 'Website Test User',
    'email' => 'website@test.com',
    'phone' => '+1234567890',
    'start_date' => $tomorrow,
    'end_date' => $nextWeek,
    'message' => 'Test booking from website simulation'
];

echo "Testing with dates:\n";
echo "Start: $tomorrow\n";
echo "End: $nextWeek\n\n";

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
    echo "ID: {$booking['id']}, Customer: {$booking['customer_name']}, Email: {$booking['email']}, Dates: {$booking['start_date']} to {$booking['end_date']}\n";
}
?>
