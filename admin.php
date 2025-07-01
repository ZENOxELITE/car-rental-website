<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Simple authentication (in production, use proper authentication)
session_start();
$admin_password = 'admin123'; // Change this password!

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = 'Invalid password';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle booking status updates
if (isset($_POST['update_status']) && isset($_SESSION['admin_logged_in'])) {
    $bookingId = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    
    if (updateBookingStatus($bookingId, $status)) {
        $success = 'Booking status updated successfully';
    } else {
        $error = 'Failed to update booking status';
    }
}

// Get all bookings
$bookings = [];
if (isset($_SESSION['admin_logged_in'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT b.*, c.name as car_name, c.price_per_day,
                   DATEDIFF(b.end_date, b.start_date) as days,
                   (DATEDIFF(b.end_date, b.start_date) * c.price_per_day) as total_cost
            FROM bookings b 
            JOIN cars c ON b.car_id = c.id 
            ORDER BY b.created_at DESC
        ");
        $bookings = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = 'Failed to load bookings: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Elite Car Rentals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3498db;
        }
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .btn-small {
            width: auto;
            padding: 5px 15px;
            font-size: 12px;
        }
        .success {
            color: green;
            background: #f0fff0;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: red;
            background: #fff0f0;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #3498db;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-pending {
            background-color: #ffeaa7;
            color: #2d3436;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-confirmed {
            background-color: #00b894;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-cancelled {
            background-color: #e17055;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .update-form {
            display: inline-block;
        }
        .update-form select {
            width: auto;
            padding: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
    <!-- Login Form -->
    <div class="login-form">
        <h2>🔐 Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 12px; color: #666;">
            Default password: admin123 (Please change this in production!)
        </p>
    </div>

<?php else: ?>
    <!-- Admin Dashboard -->
    <div class="container">
        <div class="header">
            <h1>🚗 Elite Car Rentals - Admin Panel</h1>
            <form method="post" style="margin: 0;">
                <button type="submit" name="logout" class="btn-small">Logout</button>
            </form>
        </div>

        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats">
            <?php
            $totalBookings = count($bookings);
            $pendingBookings = count(array_filter($bookings, function($b) { return $b['status'] === 'pending'; }));
            $confirmedBookings = count(array_filter($bookings, function($b) { return $b['status'] === 'confirmed'; }));
            $totalRevenue = array_sum(array_column($bookings, 'total_cost'));
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalBookings; ?></div>
                <div>Total Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $pendingBookings; ?></div>
                <div>Pending Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $confirmedBookings; ?></div>
                <div>Confirmed Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($totalRevenue, 2); ?></div>
                <div>Total Revenue</div>
            </div>
        </div>

        <!-- Bookings Table -->
        <h2>Recent Bookings</h2>
        
        <?php if (empty($bookings)): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Car</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                            <td>
                                <?php echo date('M j', strtotime($booking['start_date'])); ?> - 
                                <?php echo date('M j, Y', strtotime($booking['end_date'])); ?>
                            </td>
                            <td><?php echo $booking['days']; ?></td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                            <td>
                                <span class="status-<?php echo $booking['status']; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($booking['email']); ?></div>
                                <div><?php echo htmlspecialchars($booking['phone']); ?></div>
                            </td>
                            <td>
                                <form method="post" class="update-form">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-small">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <p>Elite Car Rentals Admin Panel | <a href="index.html">View Website</a></p>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
