<?php
require_once 'config.php';

// PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to get all cars
function getAllCars() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM cars ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

// Function to get car by ID
function getCarById($carId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$carId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return null;
    }
}

// Function to create a booking
function createBooking($bookingData) {
    try {
        $pdo = getDBConnection();
        
        // Check if car exists
        $car = getCarById($bookingData['car_id']);
        if (!$car) {
            throw new Exception("Car not found");
        }
        
        // Insert booking
        $stmt = $pdo->prepare("
            INSERT INTO bookings (car_id, customer_name, email, phone, start_date, end_date, message, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([
            $bookingData['car_id'],
            $bookingData['customer_name'],
            $bookingData['email'],
            $bookingData['phone'],
            $bookingData['start_date'],
            $bookingData['end_date'],
            $bookingData['message'] ?? ''
        ]);
        
        $bookingId = $pdo->lastInsertId();
        
        // Calculate booking details
        $startDate = new DateTime($bookingData['start_date']);
        $endDate = new DateTime($bookingData['end_date']);
        $days = $startDate->diff($endDate)->days;
        $totalCost = $days * $car['price_per_day'];
        
        // Send confirmation email
        $emailSent = sendBookingConfirmationEmail($bookingData, $car, $days, $totalCost, $bookingId);
        
        return [
            'booking_id' => $bookingId,
            'car' => $car,
            'days' => $days,
            'total_cost' => $totalCost,
            'email_sent' => $emailSent
        ];
        
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        throw $e;
    }
}

// Function to send booking confirmation email
function sendBookingConfirmationEmail($bookingData, $car, $days, $totalCost, $bookingId) {
    try {
        // For this demo, we'll use a simple mail function
        // In production, use PHPMailer or similar library
        
        $to = $bookingData['email'];
        $subject = "Booking Confirmation - Elite Car Rentals";
        
        $message = generateEmailTemplate($bookingData, $car, $days, $totalCost, $bookingId);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
            'Reply-To: ' . FROM_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Use mail() function for demo (configure your server's mail settings)
        $emailSent = mail($to, $subject, $message, implode("\r\n", $headers));
        
        // Log email attempt
        if ($emailSent) {
            error_log("Confirmation email sent to: " . $to);
        } else {
            error_log("Failed to send confirmation email to: " . $to);
        }
        
        return $emailSent;
        
    } catch (Exception $e) {
        logError("Email sending failed: " . $e->getMessage(), __FILE__, __LINE__);
        return false;
    }
}

// Function to generate email template
function generateEmailTemplate($bookingData, $car, $days, $totalCost, $bookingId) {
    $customerName = htmlspecialchars($bookingData['customer_name']);
    $carName = htmlspecialchars($car['name']);
    $startDate = date('F j, Y', strtotime($bookingData['start_date']));
    $endDate = date('F j, Y', strtotime($bookingData['end_date']));
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Booking Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #3498db; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .booking-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .footer { text-align: center; padding: 20px; color: #666; }
            .highlight { color: #3498db; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🚗 Elite Car Rentals</h1>
                <h2>Booking Confirmation</h2>
            </div>
            
            <div class='content'>
                <h3>Dear $customerName,</h3>
                
                <p>Thank you for choosing Elite Car Rentals! We're thrilled to have you as our valued customer.</p>
                
                <p>Your booking has been confirmed and we'll be reaching out to you soon with pickup details and further instructions.</p>
                
                <div class='booking-details'>
                    <h3>📋 Booking Details</h3>
                    <p><strong>Booking ID:</strong> <span class='highlight'>#ECR-$bookingId</span></p>
                    <p><strong>Vehicle:</strong> $carName</p>
                    <p><strong>Rental Period:</strong> $startDate to $endDate</p>
                    <p><strong>Duration:</strong> $days day(s)</p>
                    <p><strong>Total Cost:</strong> <span class='highlight'>$$totalCost</span></p>
                    <p><strong>Contact Email:</strong> {$bookingData['email']}</p>
                    <p><strong>Contact Phone:</strong> {$bookingData['phone']}</p>
                </div>
                
                <div class='booking-details'>
                    <h3>📞 What's Next?</h3>
                    <ul>
                        <li>Our team will contact you within 24 hours to confirm pickup details</li>
                        <li>Please have a valid driver's license and credit card ready</li>
                        <li>Arrive 15 minutes early for vehicle inspection</li>
                        <li>Contact us at <strong>(555) 123-4567</strong> if you have any questions</li>
                    </ul>
                </div>
                
                <p>We appreciate your business and look forward to providing you with an exceptional car rental experience!</p>
                
                <p>Best regards,<br>
                <strong>The Elite Car Rentals Team</strong></p>
            </div>
            
            <div class='footer'>
                <p>Elite Car Rentals | 123 Main Street, City, State 12345</p>
                <p>Phone: (555) 123-4567 | Email: info@elitecarrentals.com</p>
                <p><small>This is an automated message. Please do not reply to this email.</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

// Function to get bookings by email
function getBookingsByEmail($email) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT b.*, c.name as car_name, c.image as car_image 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.id 
            WHERE b.email = ? 
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$email]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

// Function to update booking status
function updateBookingStatus($bookingId, $status) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $bookingId]);
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return false;
    }
}

// Function to check car availability
function checkCarAvailability($carId, $startDate, $endDate, $excludeBookingId = null) {
    try {
        $pdo = getDBConnection();
        
        $sql = "
            SELECT COUNT(*) as conflicts 
            FROM bookings 
            WHERE car_id = ? 
            AND status IN ('confirmed', 'pending')
            AND (
                (start_date <= ? AND end_date > ?) OR
                (start_date < ? AND end_date >= ?) OR
                (start_date >= ? AND end_date <= ?)
            )
        ";
        
        $params = [$carId, $startDate, $startDate, $endDate, $endDate, $startDate, $endDate];
        
        if ($excludeBookingId) {
            $sql .= " AND id != ?";
            $params[] = $excludeBookingId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['conflicts'] == 0;
        
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return false;
    }
}

// Function to get popular cars
function getPopularCars($limit = 4) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, COUNT(b.id) as booking_count 
            FROM cars c 
            LEFT JOIN bookings b ON c.id = b.car_id 
            GROUP BY c.id 
            ORDER BY booking_count DESC, c.name 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}
?>
