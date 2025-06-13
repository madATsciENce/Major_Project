<?php
// process_booking.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Hotel data (same as booking.php)
$hotels = [
    "Leh Palace Hotel" => ["location" => "Leh", "rent" => 4500],
    "Snowland Hotel" => ["location" => "Leh", "rent" => 4000],
    "Lamayuru Hotel" => ["location" => "Lamayuru", "rent" => 3200],
    "Stok Palace Heritage Hotel" => ["location" => "Stok", "rent" => 7000],
    "Zanskar Valley Hotel" => ["location" => "Zanskar", "rent" => 4400],
];

// Validate and sanitize input
$hotelName = $_POST['hotel'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$guests = intval($_POST['guests'] ?? 1);

if (!$hotelName || !array_key_exists($hotelName, $hotels)) {
    die('Invalid hotel selected');
}
if (!$name || !$email || !$checkin || !$checkout || $guests < 1) {
    die('Please fill all required fields');
}

// Calculate number of nights
$checkinDate = new DateTime($checkin);
$checkoutDate = new DateTime($checkout);
$interval = $checkinDate->diff($checkoutDate);
$nights = $interval->days;
if ($nights < 1) {
    die('Check-out date must be after check-in date');
}

// Calculate total amount in INR (rent * nights)
$rent = $hotels[$hotelName]['rent'];
$totalAmount = $rent * $nights;

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

// Generate a unique order ID (you can use a UUID or timestamp-based string)
$orderId = 'order_' . uniqid();

// Insert booking record with status 'pending'
$stmt = $pdo->prepare("INSERT INTO bookings (hotel_name, customer_name, customer_email, checkin_date, checkout_date, guests, total_amount, razorpay_order_id, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$hotelName, $name, $email, $checkin, $checkout, $guests, $totalAmount, $orderId, 'pending']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment - <?php echo htmlspecialchars($hotelName); ?></title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body>
    <h2>Complete Payment for <?php echo htmlspecialchars($hotelName); ?></h2>
    <p>Total Amount: ₹<?php echo number_format($totalAmount); ?></p>

    <button id="rzp-button">Pay Now</button>

    <script>
    var options = {
        "key": "YOUR_RAZORPAY_KEY_ID", // Enter your Razorpay Key ID here
        "amount": "<?php echo $totalAmount * 100; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 100 refers to 100 paise
        "currency": "INR",
        "name": "<?php echo htmlspecialchars($hotelName); ?>",
        "description": "Hotel Booking Payment",
        "handler": function(response) {
            // On successful payment, redirect to success page with payment details
            window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id +
                "&order_id=" + response.razorpay_order_id + "&signature=" + response.razorpay_signature;
        },
        "prefill": {
            "name": "<?php echo htmlspecialchars($name); ?>",
            "email": "<?php echo htmlspecialchars($email); ?>"
        },
        "theme": {
            "color": "#3498db"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button').onclick = function(e) {
        rzp1.open();
        e.preventDefault();
    }
    </script>
</body>

</html>