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

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$booking_id) {
    header("Location: user_dashboard.php");
    exit();
}

// Get booking details
$stmt = $conn->prepare("
    SELECT b.*, 
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
    WHERE b.id = ? AND b.user_id = ?
");

$stmt->bind_param("ii", $booking_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: user_dashboard.php");
    exit();
}

$booking = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Safar</title>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .confirmation-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .confirmation-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .confirmation-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .confirmation-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .booking-details {
            padding: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        .detail-value {
            color: #666;
            text-align: right;
        }

        .total-amount {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }

        .total-amount .detail-row {
            border: none;
            padding: 0.5rem 0;
            font-size: 1.2rem;
            font-weight: bold;
            color: #3c00a0;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        @media (max-width: 768px) {
            .actions {
                flex-direction: column;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="confirmation-title">Booking Confirmed!</h1>
                <p class="confirmation-subtitle">Your booking has been successfully created</p>
            </div>

            <div class="booking-details">
                <h2 style="margin-bottom: 1.5rem; color: #333;">
                    <i class="fas fa-receipt"></i> Booking Details
                </h2>

                <div class="detail-row">
                    <span class="detail-label">Booking ID</span>
                    <span class="detail-value">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Package/Hotel</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['booking_name']); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Destination</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['destination_name']); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-in Date</span>
                    <span class="detail-value"><?php echo date('F d, Y', strtotime($booking['check_in_date'])); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-out Date</span>
                    <span class="detail-value"><?php echo date('F d, Y', strtotime($booking['check_out_date'])); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Participants</span>
                    <span class="detail-value"><?php echo $booking['participants']; ?> <?php echo $booking['participants'] === 1 ? 'Person' : 'People'; ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Booking Status</span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                            <?php echo ucfirst($booking['booking_status']); ?>
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Booking Date</span>
                    <span class="detail-value"><?php echo date('F d, Y g:i A', strtotime($booking['created_at'])); ?></span>
                </div>

                <?php if ($booking['special_requests']): ?>
                <div class="detail-row">
                    <span class="detail-label">Special Requests</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['special_requests']); ?></span>
                </div>
                <?php endif; ?>

                <div class="total-amount">
                    <div class="detail-row">
                        <span class="detail-label">Total Amount</span>
                        <span class="detail-value">₹<?php echo number_format($booking['total_amount']); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <a href="user_dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i>
                        Go to Dashboard
                    </a>
                    <a href="special_offers.php" class="btn btn-secondary">
                        <i class="fas fa-search"></i>
                        Browse More Offers
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
