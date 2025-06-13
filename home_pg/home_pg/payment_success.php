<?php
session_start();
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

$booking_id = $_GET['booking_id'] ?? 0;

if (!$booking_id) {
    header("Location: user_dashboard.php");
    exit();
}

// Get booking and payment details
$query = "SELECT b.*, p.gateway_payment_id, p.payment_method,
    CASE 
        WHEN b.booking_type = 'hotel' THEN h.name
        WHEN b.booking_type = 'package' THEN pkg.name
    END as booking_name,
    CASE 
        WHEN b.booking_type = 'hotel' THEN d1.name
        WHEN b.booking_type = 'package' THEN d2.name
    END as destination_name
    FROM bookings b
    LEFT JOIN payments p ON b.id = p.booking_id
    LEFT JOIN hotels h ON b.hotel_id = h.id
    LEFT JOIN packages pkg ON b.package_id = pkg.id
    LEFT JOIN destinations d1 ON h.destination_id = d1.id
    LEFT JOIN destinations d2 ON pkg.destination_id = d2.id
    WHERE b.id = ? AND b.user_id = ? AND b.payment_status = 'paid'";

$stmt = $conn->prepare($query);
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
    <title>Payment Successful - Safar</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            padding: 0;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            animation: checkmark 0.8s ease-in-out 0.3s both;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .success-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .booking-details {
            padding: 2rem;
        }

        .detail-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-grid {
            display: grid;
            gap: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .booking-id {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 2rem;
        }

        .booking-id h3 {
            margin-bottom: 0.5rem;
        }

        .booking-id-value {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
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
            background: #f8f9fa;
            color: #333;
            border: 2px solid #eee;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #ddd;
        }

        .confirmation-message {
            background: #e8f5e8;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .confirmation-message i {
            color: #28a745;
            font-size: 1.2rem;
        }

        .next-steps {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .next-steps h4 {
            color: #856404;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .next-steps ul {
            color: #856404;
            padding-left: 1.5rem;
        }

        .next-steps li {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .success-container {
                width: 95%;
                margin: 1rem;
            }

            .success-header {
                padding: 2rem 1rem;
            }

            .booking-details {
                padding: 1.5rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Payment Successful!</h1>
            <p class="success-subtitle">Your booking has been confirmed</p>
        </div>

        <div class="booking-details">
            <div class="booking-id">
                <h3>Booking Confirmation</h3>
                <div class="booking-id-value">SAFAR-<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>

            <div class="confirmation-message">
                <i class="fas fa-envelope"></i>
                <span>A confirmation email has been sent to <?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <div class="detail-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Booking Details
                </h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Booking Type:</span>
                        <span class="detail-value"><?php echo ucfirst($booking['booking_type']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['booking_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Destination:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['destination_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-in Date:</span>
                        <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-out Date:</span>
                        <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><?php echo $booking['booking_type'] === 'hotel' ? 'Rooms' : 'Participants'; ?>:</span>
                        <span class="detail-value"><?php echo $booking['participants']; ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Payment Details
                </h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Payment ID:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['gateway_payment_id']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value"><?php echo ucfirst($booking['payment_method']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value">₹<?php echo number_format($booking['total_amount']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value"><?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="next-steps">
                <h4>
                    <i class="fas fa-lightbulb"></i>
                    What's Next?
                </h4>
                <ul>
                    <li>You will receive a detailed itinerary via email within 24 hours</li>
                    <li>Our team will contact you 2-3 days before your trip</li>
                    <li>Keep your booking ID handy for any future reference</li>
                    <li>Check your email for important travel information</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="user_dashboard.php" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i>
                    Go to Dashboard
                </a>
                <a href="booking_system.php" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    Book Another Trip
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect to dashboard after 30 seconds
        setTimeout(() => {
            if (confirm('Would you like to go to your dashboard now?')) {
                window.location.href = 'user_dashboard.php';
            }
        }, 30000);

        // Confetti animation (optional)
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.zIndex = '9999';
                confetti.style.pointerEvents = 'none';
                confetti.style.borderRadius = '50%';
                
                document.body.appendChild(confetti);
                
                const animation = confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(100vh) rotate(${Math.random() * 360}deg)`, opacity: 0 }
                ], {
                    duration: Math.random() * 3000 + 2000,
                    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                });
                
                animation.onfinish = () => confetti.remove();
            }
        }

        // Trigger confetti on page load
        window.addEventListener('load', () => {
            setTimeout(createConfetti, 500);
        });
    </script>
</body>
</html>
