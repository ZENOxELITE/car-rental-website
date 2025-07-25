<?php
header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/file_db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

try {
    // Get and validate input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Required fields
    $requiredFields = ['car_id', 'customer_name', 'email', 'phone', 'start_date', 'end_date'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            sendErrorResponse("Missing required field: $field");
        }
    }
    
    // Sanitize inputs
    $bookingData = [
        'car_id' => (int)$input['car_id'],
        'customer_name' => sanitizeInput($input['customer_name']),
        'email' => sanitizeInput($input['email']),
        'phone' => sanitizeInput($input['phone']),
        'start_date' => sanitizeInput($input['start_date']),
        'end_date' => sanitizeInput($input['end_date']),
        'message' => isset($input['message']) ? sanitizeInput($input['message']) : ''
    ];
    
    // Validate email
    if (!validateEmail($bookingData['email'])) {
        sendErrorResponse('Invalid email address');
    }
    
    // Validate phone
    if (!validatePhone($bookingData['phone'])) {
        sendErrorResponse('Invalid phone number');
    }
    
    // Validate dates
    if (!validateDate($bookingData['start_date']) || !validateDate($bookingData['end_date'])) {
        sendErrorResponse('Invalid date format');
    }
    
    // Check if start date is in the future
    $startDate = new DateTime($bookingData['start_date']);
    $endDate = new DateTime($bookingData['end_date']);
    $today = new DateTime();
    
    if ($startDate < $today) {
        sendErrorResponse('Start date cannot be in the past');
    }
    
    // Check if end date is after start date
    if ($endDate <= $startDate) {
        sendErrorResponse('End date must be after start date');
    }
    
    // Get car details and create booking
    $db = new FileDB();
    $car = $db->getCarById($bookingData['car_id']);
    
    if (!$car) {
        sendErrorResponse('Car not found');
    }
    
    // Calculate rental details
    $days = $endDate->diff($startDate)->days;
    $totalCost = $days * $car['price_per_day'];
    
    // Create booking
    $booking = $db->addBooking($bookingData);
    
    $result = [
        'booking_id' => $booking['id'],
        'car' => $car,
        'days' => $days,
        'total_cost' => $totalCost,
        'email_sent' => false // Email functionality can be added later
    ];
    
    // Send success response
    sendSuccessResponse('Booking created successfully', [
        'booking_id' => $result['booking_id'],
        'car' => $result['car'],
        'days' => $result['days'],
        'total_cost' => $result['total_cost'],
        'email_sent' => $result['email_sent']
    ]);
    
} catch (Exception $e) {
    logError($e->getMessage(), __FILE__, __LINE__);
    sendErrorResponse('Failed to process booking. Please try again later.', 500);
}

?>
