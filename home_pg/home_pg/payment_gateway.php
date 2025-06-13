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
    header("Location: booking_system.php");
    exit();
}

// Get booking details
$booking_query = "SELECT b.*, 
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
    WHERE b.id = ? AND b.user_id = ?";

$stmt = $conn->prepare($booking_query);
$stmt->bind_param("ii", $booking_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: booking_system.php");
    exit();
}

$booking = $result->fetch_assoc();

// Handle payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $payment_method = $_POST['payment_method'];
    
    // Simulate payment processing (In production, integrate with actual payment gateway)
    $payment_success = true; // This would be determined by the actual payment gateway response
    
    if ($payment_success) {
        // Create payment record
        $payment_id = 'PAY_' . time() . '_' . $booking_id;
        $insert_payment = "INSERT INTO payments (booking_id, payment_gateway, gateway_payment_id, amount, payment_status, payment_method) 
                          VALUES (?, 'razorpay', ?, ?, 'success', ?)";
        $stmt = $conn->prepare($insert_payment);
        $stmt->bind_param("isds", $booking_id, $payment_id, $booking['total_amount'], $payment_method);
        $stmt->execute();
        
        // Update booking status
        $update_booking = "UPDATE bookings SET booking_status = 'confirmed', payment_status = 'paid' WHERE id = ?";
        $stmt = $conn->prepare($update_booking);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        
        // Redirect to success page
        header("Location: payment_success.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error_message = "Payment failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Safar</title>
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
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .payment-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .payment-header {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .payment-header p {
            opacity: 0.9;
        }

        .payment-content {
            padding: 2rem;
        }

        .booking-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-title {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
        }

        .summary-row.total {
            border-top: 2px solid #eee;
            margin-top: 1rem;
            padding-top: 1rem;
            font-weight: bold;
            font-size: 1.2rem;
            color: #3c00a0;
        }

        .payment-methods {
            margin-bottom: 2rem;
        }

        .method-title {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .payment-options {
            display: grid;
            gap: 1rem;
        }

        .payment-option {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .payment-option:hover {
            border-color: #3c00a0;
        }

        .payment-option.selected {
            border-color: #3c00a0;
            background: #f0f8ff;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3c00a0, #667eea);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .payment-info h4 {
            color: #333;
            margin-bottom: 0.25rem;
        }

        .payment-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .card-form {
            display: none;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .card-form.active {
            display: block;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3c00a0;
        }

        .security-info {
            background: #e8f5e8;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .security-info i {
            color: #28a745;
        }

        .pay-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
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
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h1><i class="fas fa-credit-card"></i> Secure Payment</h1>
                <p>Complete your booking with our secure payment system</p>
            </div>

            <div class="payment-content">
                <?php if (isset($error_message)): ?>
                    <div class="alert"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="booking-summary">
                    <h3 class="summary-title">
                        <i class="fas fa-receipt"></i>
                        Booking Summary
                    </h3>
                    <div class="summary-row">
                        <span><strong><?php echo htmlspecialchars($booking['booking_name']); ?></strong></span>
                        <span><?php echo ucfirst($booking['booking_type']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Destination:</span>
                        <span><?php echo htmlspecialchars($booking['destination_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Check-in:</span>
                        <span><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Check-out:</span>
                        <span><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?php echo $booking['booking_type'] === 'hotel' ? 'Rooms' : 'Participants'; ?>:</span>
                        <span><?php echo $booking['participants']; ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total Amount:</span>
                        <span>₹<?php echo number_format($booking['total_amount']); ?></span>
                    </div>
                </div>

                <form method="POST" id="payment-form">
                    <div class="payment-methods">
                        <h3 class="method-title">
                            <i class="fas fa-wallet"></i>
                            Choose Payment Method
                        </h3>
                        
                        <div class="payment-options">
                            <label class="payment-option" for="card">
                                <input type="radio" id="card" name="payment_method" value="card" checked>
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Credit/Debit Card</h4>
                                    <p>Visa, Mastercard, RuPay accepted</p>
                                </div>
                            </label>

                            <label class="payment-option" for="upi">
                                <input type="radio" id="upi" name="payment_method" value="upi">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>UPI Payment</h4>
                                    <p>Pay using Google Pay, PhonePe, Paytm</p>
                                </div>
                            </label>

                            <label class="payment-option" for="netbanking">
                                <input type="radio" id="netbanking" name="payment_method" value="netbanking">
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Net Banking</h4>
                                    <p>All major banks supported</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="card-form active" id="card-form">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" id="expiry" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" placeholder="123" maxlength="3">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="card_name">Cardholder Name</label>
                            <input type="text" id="card_name" placeholder="Name as on card">
                        </div>
                    </div>

                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Your payment information is encrypted and secure</span>
                    </div>

                    <button type="submit" name="process_payment" class="pay-btn">
                        <i class="fas fa-lock"></i>
                        Pay ₹<?php echo number_format($booking['total_amount']); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected class from all options
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.classList.remove('selected');
                });
                
                // Add selected class to current option
                this.closest('.payment-option').classList.add('selected');
                
                // Show/hide card form
                const cardForm = document.getElementById('card-form');
                if (this.value === 'card') {
                    cardForm.classList.add('active');
                } else {
                    cardForm.classList.remove('active');
                }
            });
        });

        // Card number formatting
        document.getElementById('card_number').addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = formattedValue;
        });

        // Expiry date formatting
        document.getElementById('expiry').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });

        // CVV validation
        document.getElementById('cvv').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Initialize first option as selected
        document.querySelector('.payment-option').classList.add('selected');
    </script>
</body>
</html>
