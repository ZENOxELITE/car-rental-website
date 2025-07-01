<?php
// Database configuration (MySQL)
define('DB_HOST', 'localhost');
define('DB_NAME', 'car_rental');
define('DB_USER', 'root');
define('DB_PASS', '');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('FROM_EMAIL', 'noreply@elitecarrentals.com');
define('FROM_NAME', 'Elite Car Rentals');

// Site configuration
define('SITE_URL', 'http://localhost:8000');
define('SITE_NAME', 'Elite Car Rentals');

// Create database connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        // Set charset after connection
        $pdo->exec("SET NAMES utf8mb4");
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

// Error logging function
function logError($message, $file = '', $line = '') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] Error in $file:$line - $message" . PHP_EOL;
    error_log($logMessage, 3, 'errors.log');
}

// Success response function
function sendSuccessResponse($message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Error response function
function sendErrorResponse($message, $code = 400) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

// Sanitize input function
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Validate email function
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone function
function validatePhone($phone) {
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
}

// Validate date function
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>
