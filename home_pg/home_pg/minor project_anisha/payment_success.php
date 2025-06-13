<?php
// payment_success.php

// Retrieve payment details from query parameters
$paymentId = $_GET['payment_id'] ?? null;
$orderId = $_GET['order_id'] ?? null;
$signature = $_GET['signature'] ?? null;

if (!$paymentId || !$orderId || !$signature) {
    echo "Invalid payment details.";
    exit;
}

// Connect to database (update with your DB credentials)
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_db_username';
$password = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch booking by razorpay_order_id
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE razorpay_order_id = ?");
$stmt->execute([$orderId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "Booking not found.";
    exit;
}

// Check payment status
if ($booking['payment_status'] !== 'captured') {
    echo "Payment not confirmed yet. Please wait for confirmation.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment Success</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #eef2f3;
        padding: 20px;
        text-align: center;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        display: inline-block;
        padding: 20px;
        border-radius: 8px;
        margin-top: 50px;
    }

    a {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        color: #3498db;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="success-message">
        <h2>Payment Successful!</h2>
        <p>Your payment ID: <?php echo htmlspecialchars($paymentId); ?></p>
        <p>Thank you for booking with us. We will contact you shortly with the booking details.</p>
        <p><strong>Hotel:</strong> <?php echo htmlspecialchars($booking['hotel_name']); ?></p>
        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($booking['checkin_date']); ?></p>
        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($booking['checkout_date']); ?></p>
        <p><strong>Guests:</strong> <?php echo htmlspecialchars($booking['guests']); ?></p>
        <a href="le ladakh.html">Back to Hotels</a>
    </div>
</body>

</html>