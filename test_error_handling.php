<?php
require_once 'includes/config.php';
require_once 'includes/file_db.php';

echo "Testing Error Handling and Edge Cases\n";
echo "====================================\n\n";

$db = new FileDB();

// Test 1: Invalid car ID
echo "1. Testing invalid car ID:\n";
$invalidCar = $db->getCarById(999);
if ($invalidCar === null) {
    echo "✓ Correctly returned null for invalid car ID\n\n";
} else {
    echo "✗ Should return null for invalid car ID\n\n";
}

// Test 2: Test booking with invalid data
echo "2. Testing booking validation:\n";

// Test invalid email
$invalidBooking = [
    'car_id' => 1,
    'customer_name' => 'Test User',
    'email' => 'invalid-email',
    'phone' => '+1234567890',
    'start_date' => '2024-07-01',
    'end_date' => '2024-07-05',
    'message' => 'Test'
];

echo "Testing email validation: ";
if (validateEmail($invalidBooking['email'])) {
    echo "✗ Should reject invalid email\n";
} else {
    echo "✓ Correctly rejected invalid email\n";
}

// Test invalid phone
$invalidBooking['phone'] = 'invalid-phone';
echo "Testing phone validation: ";
if (validatePhone($invalidBooking['phone'])) {
    echo "✗ Should reject invalid phone\n";
} else {
    echo "✓ Correctly rejected invalid phone\n";
}

// Test invalid date
echo "Testing date validation: ";
if (validateDate('invalid-date')) {
    echo "✗ Should reject invalid date\n";
} else {
    echo "✓ Correctly rejected invalid date\n";
}

echo "\n";

// Test 3: Multiple bookings
echo "3. Testing multiple bookings:\n";
for ($i = 1; $i <= 3; $i++) {
    $booking = [
        'car_id' => ($i % 8) + 1, // Cycle through car IDs
        'customer_name' => "Customer $i",
        'email' => "customer$i@example.com",
        'phone' => "+123456789$i",
        'start_date' => '2024-07-0' . ($i + 5),
        'end_date' => '2024-07-' . ($i + 10),
        'message' => "Booking $i"
    ];
    
    $result = $db->addBooking($booking);
    echo "Booking $i created with ID: " . $result['id'] . "\n";
}

$allBookings = $db->getBookings();
echo "Total bookings now: " . count($allBookings) . "\n\n";

// Test 4: Special characters in names
echo "4. Testing special characters:\n";
$specialBooking = [
    'car_id' => 2,
    'customer_name' => 'José María O\'Connor-Smith',
    'email' => 'jose.maria@example.com',
    'phone' => '+34-123-456-789',
    'start_date' => '2024-08-01',
    'end_date' => '2024-08-05',
    'message' => 'Special chars: àáâãäåæçèéêë & symbols!'
];

$specialResult = $db->addBooking($specialBooking);
echo "Special character booking created with ID: " . $specialResult['id'] . "\n";
echo "Customer name stored: " . $specialResult['customer_name'] . "\n\n";

// Test 5: Empty and null values
echo "5. Testing input sanitization:\n";
$testInputs = [
    '  Normal Text  ',
    '<script>alert("xss")</script>',
    'Text with "quotes" and \'apostrophes\'',
    ''
];

foreach ($testInputs as $input) {
    $sanitized = sanitizeInput($input);
    echo "Input: '$input' -> Sanitized: '$sanitized'\n";
}

echo "\nAll error handling tests completed!\n";
?>
