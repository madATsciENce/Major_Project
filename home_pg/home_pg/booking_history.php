<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_handler.php';

$auth = new AuthHandler();
if (!$auth->isLoggedIn()) {
    header("Location: home3.html");
    exit();
}

$user = $auth->getCurrentUser();

// Database connection
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user's bookings
$bookings_query = "SELECT b.*, 
    CASE 
        WHEN b.booking_type = 'hotel' THEN h.name
        WHEN b.booking_type = 'package' THEN p.name
    END as booking_name,
    CASE 
        WHEN b.booking_type = 'hotel' THEN d1.name
        WHEN b.booking_type = 'package' THEN d2.name
    END as destination_name
    FROM bookings b
    LEFT JOIN hotels h ON b.hotel_id = h.id
    LEFT JOIN packages p ON b.package_id = p.id
    LEFT JOIN destinations d1 ON h.destination_id = d1.id
    LEFT JOIN destinations d2 ON p.destination_id = d2.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC";

$stmt = $conn->prepare($bookings_query);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - Safar</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #3c00a0;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #3c00a0;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .page-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .bookings-container {
            display: grid;
            gap: 1.5rem;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-2px);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .booking-info h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .booking-destination {
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .booking-id {
            font-family: 'Courier New', monospace;
            color: #888;
            font-size: 0.9rem;
        }

        .booking-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .booking-amount {
            font-size: 1.3rem;
            font-weight: bold;
            color: #3c00a0;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .booking-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #3c00a0;
            color: white;
        }

        .btn-primary:hover {
            background: #290073;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .no-bookings {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .no-bookings i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .no-bookings h3 {
            color: #333;
            margin-bottom: 1rem;
        }

        .no-bookings p {
            color: #666;
            margin-bottom: 2rem;
        }

        .book-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .book-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .booking-header {
                flex-direction: column;
                gap: 1rem;
            }

            .booking-status {
                align-items: flex-start;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="home3.html" class="logo">Safar</a>
            <div class="nav-links">
                <a href="home3.html"><i class="fas fa-home"></i> Home</a>
                <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="booking_system.php"><i class="fas fa-plus"></i> New Booking</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Booking History</h1>
            <p class="page-subtitle">View and manage all your travel bookings</p>
        </div>

        <div class="bookings-container">
            <?php if ($bookings->num_rows > 0): ?>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <div class="booking-info">
                                <h3><?php echo htmlspecialchars($booking['booking_name']); ?></h3>
                                <div class="booking-destination">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($booking['destination_name']); ?>
                                </div>
                                <div class="booking-id">
                                    Booking ID: SAFAR-<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?>
                                </div>
                            </div>
                            <div class="booking-status">
                                <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                    <?php echo ucfirst($booking['booking_status']); ?>
                                </span>
                                <div class="booking-amount">
                                    ₹<?php echo number_format($booking['total_amount']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="booking-details">
                            <div class="detail-item">
                                <span class="detail-label">Type</span>
                                <span class="detail-value"><?php echo ucfirst($booking['booking_type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-in</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-out</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?php echo $booking['booking_type'] === 'hotel' ? 'Rooms' : 'Participants'; ?></span>
                                <span class="detail-value"><?php echo $booking['participants']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Booked On</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Payment Status</span>
                                <span class="detail-value"><?php echo ucfirst($booking['payment_status']); ?></span>
                            </div>
                        </div>

                        <div class="booking-actions">
                            <a href="#" class="action-btn btn-primary">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>

                            <?php if ($booking['booking_status'] === 'confirmed'): ?>
                                <a href="#" class="action-btn btn-secondary">
                                    <i class="fas fa-download"></i>
                                    Download Voucher
                                </a>
                            <?php endif; ?>

                            <?php if ($booking['booking_status'] === 'pending'): ?>
                                <a href="#" class="action-btn btn-danger" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            <?php endif; ?>

                            <?php if ($booking['booking_status'] === 'completed'): ?>
                                <a href="#" class="action-btn btn-secondary">
                                    <i class="fas fa-star"></i>
                                    Write Review
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No bookings found</h3>
                    <p>You haven't made any bookings yet. Start planning your next adventure!</p>
                    <a href="booking_system.php" class="book-now-btn">
                        <i class="fas fa-plus"></i>
                        Book Your First Trip
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        async function logout() {
            try {
                const response = await fetch('auth_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=logout'
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = 'home3.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        }

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                // Implement booking cancellation
                alert('Booking cancellation functionality will be implemented soon!');
            }
        }
    </script>
</body>

</html>