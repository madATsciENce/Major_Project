<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth_handler.php';

$auth = new AuthHandler();
$user = $auth->getCurrentUser();

// Database connection
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Special offers data
$special_offers = [
    1 => [
        'title' => 'Rishikesh Adventure Special',
        'image' => 'cara1.jpg',
        'original_price' => 3500,
        'discount_percent' => 25,
        'description' => 'Experience the thrill of river rafting, bungee jumping, and spiritual retreats in the adventure capital of India.',
        'features' => ['River Rafting', 'Bungee Jumping', 'Temple Visits', 'Yoga Sessions', 'Camping'],
        'duration' => '3 Days / 2 Nights',
        'destination_id' => 1,
        'package_type' => 'adventure'
    ],
    2 => [
        'title' => 'Wayanad Wildlife Escape',
        'image' => 'cara2.jpg',
        'original_price' => 4200,
        'discount_percent' => 30,
        'description' => 'Immerse yourself in the lush greenery and wildlife of Wayanad with exclusive safari experiences.',
        'features' => ['Wildlife Safari', 'Spice Plantation Tour', 'Waterfall Trekking', 'Tree House Stay', 'Nature Walks'],
        'duration' => '4 Days / 3 Nights',
        'destination_id' => 2,
        'package_type' => 'wildlife'
    ],
    3 => [
        'title' => 'Darjeeling Tea Garden Tour',
        'image' => 'cara3.jpg',
        'original_price' => 5000,
        'discount_percent' => 20,
        'description' => 'Discover the queen of hills with breathtaking mountain views and world-famous tea gardens.',
        'features' => ['Tea Garden Tours', 'Toy Train Ride', 'Tiger Hill Sunrise', 'Monastery Visits', 'Local Cuisine'],
        'duration' => '5 Days / 4 Nights',
        'destination_id' => 3,
        'package_type' => 'mountain'
    ],
    4 => [
        'title' => 'Digha Beach Holiday',
        'image' => 'cara4.jpg',
        'original_price' => 2800,
        'discount_percent' => 35,
        'description' => 'Relax on the golden beaches of Digha with water sports and fresh seafood experiences.',
        'features' => ['Beach Activities', 'Water Sports', 'Seafood Dining', 'Sunset Views', 'Local Markets'],
        'duration' => '2 Days / 1 Night',
        'destination_id' => 4,
        'package_type' => 'beach'
    ]
];

$offer_id = isset($_GET['offer']) ? intval($_GET['offer']) : 1;
$current_offer = $special_offers[$offer_id] ?? $special_offers[1];

$discounted_price = $current_offer['original_price'] - ($current_offer['original_price'] * $current_offer['discount_percent'] / 100);
$savings = $current_offer['original_price'] - $discounted_price;

// Handle booking
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now'])) {
    if (!$user) {
        $message = "Please sign in to book this package.";
        $message_type = 'error';
    } else {
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $participants = intval($_POST['participants']);
        $special_requests = $_POST['special_requests'] ?? '';

        $total_amount = $discounted_price * $participants;

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, booking_type, package_id, check_in_date, check_out_date, participants, total_amount, special_requests) VALUES (?, 'package', ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissids", $user['id'], $current_offer['destination_id'], $check_in, $check_out, $participants, $total_amount, $special_requests);

        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            header("Location: booking_confirmation.php?id=" . $booking_id);
            exit();
        } else {
            $message = "Error creating booking. Please try again.";
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_offer['title']); ?> - Special Offer</title>
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

        .offer-header {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .offer-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            position: relative;
        }

        .discount-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            z-index: 10;
        }

        .offer-content {
            padding: 2rem;
        }

        .offer-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .offer-description {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .price-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .original-price {
            font-size: 1.5rem;
            color: #999;
            text-decoration: line-through;
        }

        .discounted-price {
            font-size: 2.5rem;
            color: #3c00a0;
            font-weight: bold;
        }

        .savings {
            background: #d4edda;
            color: #155724;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .offer-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .details-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .features-list {
            list-style: none;
            margin-bottom: 2rem;
        }

        .features-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list i {
            color: #4CAF50;
        }

        .booking-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3c00a0;
            box-shadow: 0 0 0 3px rgba(60, 0, 160, 0.1);
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(60, 0, 160, 0.3);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .other-offers {
            margin-top: 3rem;
        }

        .offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .offer-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .offer-card:hover {
            transform: translateY(-5px);
        }

        .offer-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .offer-card-content {
            padding: 1rem;
        }

        .offer-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .offer-card-discount {
            color: #ff6b6b;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .offer-details {
                grid-template-columns: 1fr;
            }

            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .price-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="home3.html" class="logo">Safar</a>
            <div class="nav-links">
                <a href="home3.html"><i class="fas fa-home"></i> Home</a>
                <?php if ($user): ?>
                    <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="#" onclick="showLoginModal()"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Offer Header -->
        <div class="offer-header">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars($current_offer['image']); ?>" alt="<?php echo htmlspecialchars($current_offer['title']); ?>" class="offer-image">
                <div class="discount-badge">
                    <?php echo $current_offer['discount_percent']; ?>% OFF
                </div>
            </div>

            <div class="offer-content">
                <h1 class="offer-title"><?php echo htmlspecialchars($current_offer['title']); ?></h1>
                <p class="offer-description"><?php echo htmlspecialchars($current_offer['description']); ?></p>

                <div class="price-section">
                    <span class="original-price">₹<?php echo number_format($current_offer['original_price']); ?></span>
                    <span class="discounted-price">₹<?php echo number_format($discounted_price); ?></span>
                    <span class="savings">Save ₹<?php echo number_format($savings); ?></span>
                </div>
            </div>
        </div>

        <!-- Offer Details and Booking -->
        <div class="offer-details">
            <div class="details-section">
                <h2 class="section-title">
                    <i class="fas fa-star"></i>
                    Package Highlights
                </h2>

                <ul class="features-list">
                    <?php foreach ($current_offer['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <?php echo htmlspecialchars($feature); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem;">
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; text-align: center;">
                        <i class="fas fa-clock" style="color: #3c00a0; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($current_offer['duration']); ?></div>
                        <div style="color: #666; font-size: 0.9rem;">Duration</div>
                    </div>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; text-align: center;">
                        <i class="fas fa-map-marker-alt" style="color: #3c00a0; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                        <div style="font-weight: 600;"><?php echo ucfirst($current_offer['package_type']); ?></div>
                        <div style="color: #666; font-size: 0.9rem;">Category</div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <h2 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    Book This Offer
                </h2>

                <form method="post">
                    <div class="form-group">
                        <label class="form-label" for="check_in">Check-in Date</label>
                        <input type="date" id="check_in" name="check_in" class="form-input"
                            min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="check_out">Check-out Date</label>
                        <input type="date" id="check_out" name="check_out" class="form-input"
                            min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="participants">Number of Participants</label>
                        <select id="participants" name="participants" class="form-input" required onchange="updateTotal()">
                            <option value="">Select participants</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i === 1 ? 'Person' : 'People'; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="special_requests">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" class="form-input"
                            rows="3" placeholder="Any special requirements or requests..."></textarea>
                    </div>

                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Price per person:</span>
                            <span>₹<?php echo number_format($discounted_price); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem; color: #3c00a0;">
                            <span>Total Amount:</span>
                            <span id="total-amount">₹0</span>
                        </div>
                    </div>

                    <?php if ($user): ?>
                        <button type="submit" name="book_now" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i>
                            Book Now
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary" onclick="showLoginModal()">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In to Book
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Other Offers -->
        <div class="other-offers">
            <h2 class="section-title">
                <i class="fas fa-fire"></i>
                Other Special Offers
            </h2>

            <div class="offers-grid">
                <?php foreach ($special_offers as $id => $offer): ?>
                    <?php if ($id !== $offer_id): ?>
                        <div class="offer-card" onclick="window.location.href='special_offers.php?offer=<?php echo $id; ?>'">
                            <img src="<?php echo htmlspecialchars($offer['image']); ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>">
                            <div class="offer-card-content">
                                <div class="offer-card-title"><?php echo htmlspecialchars($offer['title']); ?></div>
                                <div class="offer-card-discount"><?php echo $offer['discount_percent']; ?>% OFF</div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function updateTotal() {
            const participants = document.getElementById('participants').value;
            const pricePerPerson = <?php echo $discounted_price; ?>;
            const totalAmount = participants ? participants * pricePerPerson : 0;

            document.getElementById('total-amount').textContent = '₹' + totalAmount.toLocaleString();
        }

        // Set minimum checkout date based on checkin
        document.getElementById('check_in').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            checkinDate.setDate(checkinDate.getDate() + 1);
            document.getElementById('check_out').min = checkinDate.toISOString().split('T')[0];
        });

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

        function showLoginModal() {
            // Redirect to home page with login modal
            window.location.href = 'home3.html#login';
        }
    </script>
</body>

</html>