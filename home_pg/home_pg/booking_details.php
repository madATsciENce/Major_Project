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

$booking_type = $_GET['type'] ?? '';
$item_id = $_GET['id'] ?? 0;

if (!$booking_type || !$item_id) {
    header("Location: booking_system.php");
    exit();
}

// Get item details
if ($booking_type === 'hotel') {
    $query = "SELECT h.*, d.name as destination_name, d.category 
              FROM hotels h 
              JOIN destinations d ON h.destination_id = d.id 
              WHERE h.id = ? AND h.status = 'active'";
} else {
    $query = "SELECT p.*, d.name as destination_name, d.category 
              FROM packages p 
              JOIN destinations d ON p.destination_id = d.id 
              WHERE p.id = ? AND p.status = 'active'";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: booking_system.php");
    exit();
}

$item = $result->fetch_assoc();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now'])) {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $participants = intval($_POST['participants']);
    $special_requests = $_POST['special_requests'] ?? '';
    
    // Calculate total amount
    $price_per_unit = $booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person'];
    
    if ($booking_type === 'hotel') {
        $days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        $total_amount = $price_per_unit * $days;
    } else {
        $total_amount = $price_per_unit * $participants;
    }
    
    // Insert booking
    $hotel_id = $booking_type === 'hotel' ? $item_id : null;
    $package_id = $booking_type === 'package' ? $item_id : null;
    
    $insert_query = "INSERT INTO bookings (user_id, booking_type, hotel_id, package_id, check_in_date, check_out_date, participants, total_amount, special_requests) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("issiisids", $user['id'], $booking_type, $hotel_id, $package_id, $check_in, $check_out, $participants, $total_amount, $special_requests);
    
    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;
        header("Location: payment_gateway.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error_message = "Booking failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($item['name']); ?> - Safar</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .booking-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        .item-details {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .item-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .item-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3c00a0, #667eea);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .item-info h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .item-location {
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .item-price {
            font-size: 2rem;
            font-weight: bold;
            color: #3c00a0;
            margin: 1rem 0;
        }

        .item-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .stars {
            color: #ffc107;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .feature-item i {
            color: #3c00a0;
        }

        .booking-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .form-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3c00a0;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .price-breakdown {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .price-total {
            border-top: 2px solid #eee;
            padding-top: 0.5rem;
            font-weight: bold;
            font-size: 1.2rem;
            color: #3c00a0;
        }

        .book-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
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

            .booking-container {
                grid-template-columns: 1fr;
            }

            .item-header {
                flex-direction: column;
                text-align: center;
            }

            .date-grid {
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
                <a href="booking_system.php"><i class="fas fa-arrow-left"></i> Back to Booking</a>
                <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="booking-container">
            <div class="item-details">
                <div class="item-header">
                    <div class="item-icon">
                        <?php if ($booking_type === 'hotel'): ?>
                            <i class="fas fa-bed"></i>
                        <?php else: ?>
                            <i class="fas fa-map-marked-alt"></i>
                        <?php endif; ?>
                    </div>
                    <div class="item-info">
                        <h1><?php echo htmlspecialchars($item['name']); ?></h1>
                        <div class="item-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($item['destination_name']); ?>
                        </div>
                    </div>
                </div>

                <div class="item-price">
                    ₹<?php echo number_format($booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person']); ?>
                    <span style="font-size: 1rem; color: #666;">
                        /<?php echo $booking_type === 'hotel' ? 'night' : 'person'; ?>
                    </span>
                </div>

                <div class="item-rating">
                    <div class="stars">
                        <?php 
                        $rating = $item['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($i - 0.5 <= $rating) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <span><?php echo number_format($rating, 1); ?> (<?php echo $item['total_reviews']; ?> reviews)</span>
                </div>

                <div class="features-grid">
                    <?php if ($booking_type === 'hotel'): ?>
                        <div class="feature-item">
                            <i class="fas fa-bed"></i>
                            <span>Comfortable Rooms</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-wifi"></i>
                            <span>Free WiFi</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-car"></i>
                            <span>Parking Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-utensils"></i>
                            <span>Restaurant</span>
                        </div>
                    <?php else: ?>
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo $item['duration_days']; ?> Days Trip</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <span>Max <?php echo $item['max_participants']; ?> People</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Guided Tour</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-camera"></i>
                            <span>Photo Opportunities</span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($item['description'])): ?>
                    <div style="margin-top: 2rem;">
                        <h3 style="margin-bottom: 1rem; color: #333;">Description</h3>
                        <p style="color: #666; line-height: 1.6;"><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="booking-form">
                <h2 class="form-title">Book Your <?php echo ucfirst($booking_type); ?></h2>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" id="booking-form">
                    <div class="date-grid">
                        <div class="form-group">
                            <label for="check_in">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="check_out">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="participants">
                            <?php echo $booking_type === 'hotel' ? 'Number of Rooms' : 'Number of Participants'; ?>
                        </label>
                        <select id="participants" name="participants" required>
                            <?php for ($i = 1; $i <= ($booking_type === 'hotel' ? 5 : $item['max_participants']); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" rows="3" placeholder="Any special requirements or requests..."></textarea>
                    </div>

                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Price per <?php echo $booking_type === 'hotel' ? 'night' : 'person'; ?>:</span>
                            <span>₹<?php echo number_format($booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person']); ?></span>
                        </div>
                        <div class="price-row">
                            <span id="quantity-label">Quantity:</span>
                            <span id="quantity-value">1</span>
                        </div>
                        <div class="price-row">
                            <span id="duration-label" style="display: none;">Duration:</span>
                            <span id="duration-value" style="display: none;">1 night</span>
                        </div>
                        <div class="price-row price-total">
                            <span>Total Amount:</span>
                            <span id="total-amount">₹<?php echo number_format($booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person']); ?></span>
                        </div>
                    </div>

                    <button type="submit" name="book_now" class="book-btn">
                        <i class="fas fa-credit-card"></i>
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const bookingType = '<?php echo $booking_type; ?>';
        const pricePerUnit = <?php echo $booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person']; ?>;

        function updatePrice() {
            const participants = parseInt(document.getElementById('participants').value);
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;

            document.getElementById('quantity-value').textContent = participants;

            let total = pricePerUnit * participants;

            if (bookingType === 'hotel' && checkIn && checkOut) {
                const days = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                if (days > 0) {
                    total = pricePerUnit * days;
                    document.getElementById('duration-label').style.display = 'block';
                    document.getElementById('duration-value').style.display = 'block';
                    document.getElementById('duration-value').textContent = days + ' night' + (days > 1 ? 's' : '');
                }
            }

            document.getElementById('total-amount').textContent = '₹' + total.toLocaleString();
        }

        // Set minimum checkout date
        document.getElementById('check_in').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            document.getElementById('check_out').min = checkInDate.toISOString().split('T')[0];
            updatePrice();
        });

        document.getElementById('check_out').addEventListener('change', updatePrice);
        document.getElementById('participants').addEventListener('change', updatePrice);

        // Initialize
        updatePrice();
    </script>
</body>
</html>
