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

$booking_type = $_GET['type'] ?? 'package';
$destination_filter = $_GET['destination'] ?? '';
$category_filter = $_GET['category'] ?? '';

// Get destinations for filter
$destinations_query = "SELECT DISTINCT id, name FROM destinations WHERE status = 'active' ORDER BY name";
$destinations_result = $conn->query($destinations_query);

// Get categories for filter
$categories = ['Mountain', 'Beach', 'Wildlife', 'Camping', 'Monuments', 'Adventure'];

// Build query based on booking type
if ($booking_type === 'hotel') {
    $query = "SELECT h.*, d.name as destination_name, d.category 
              FROM hotels h 
              JOIN destinations d ON h.destination_id = d.id 
              WHERE h.status = 'active'";

    if ($destination_filter) {
        $query .= " AND d.id = " . intval($destination_filter);
    }
    if ($category_filter) {
        $query .= " AND d.category = '" . $conn->real_escape_string($category_filter) . "'";
    }
    $query .= " ORDER BY h.rating DESC, h.name";
} else {
    $query = "SELECT p.*, d.name as destination_name, d.category 
              FROM packages p 
              JOIN destinations d ON p.destination_id = d.id 
              WHERE p.status = 'active'";

    if ($destination_filter) {
        $query .= " AND d.id = " . intval($destination_filter);
    }
    if ($category_filter) {
        $query .= " AND d.category = '" . $conn->real_escape_string($category_filter) . "'";
    }
    $query .= " ORDER BY p.rating DESC, p.name";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Trip - Safar</title>
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

        .header-section {
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

        .booking-tabs {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
        }

        .tab-btn {
            padding: 1rem 2rem;
            background: white;
            border: 2px solid #3c00a0;
            color: #3c00a0;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: #3c00a0;
            color: white;
        }

        .tab-btn:hover {
            background: #3c00a0;
            color: white;
        }

        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .filter-group select {
            padding: 0.75rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #3c00a0;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            background: #3c00a0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }

        .filter-btn:hover {
            background: #290073;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .item-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .item-card:hover {
            transform: translateY(-5px);
        }

        .item-image {
            height: 200px;
            background: linear-gradient(45deg, #3c00a0, #667eea);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .item-content {
            padding: 1.5rem;
        }

        .item-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .item-destination {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .item-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3c00a0;
        }

        .item-duration {
            color: #666;
            font-size: 0.9rem;
        }

        .item-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            color: #ffc107;
        }

        .rating-text {
            color: #666;
            font-size: 0.9rem;
        }

        .book-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .booking-tabs {
                flex-direction: column;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .items-grid {
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
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1 class="page-title">Book Your Perfect Trip</h1>
            <p class="page-subtitle">Choose from our amazing packages and hotels</p>
        </div>

        <div class="booking-tabs">
            <a href="?type=package" class="tab-btn <?php echo $booking_type === 'package' ? 'active' : ''; ?>">
                <i class="fas fa-map-marked-alt"></i> Travel Packages
            </a>
            <a href="?type=hotel" class="tab-btn <?php echo $booking_type === 'hotel' ? 'active' : ''; ?>">
                <i class="fas fa-bed"></i> Hotels
            </a>
        </div>

        <div class="filters-section">
            <form method="GET" class="filters-grid">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($booking_type); ?>">

                <div class="filter-group">
                    <label for="destination">Destination</label>
                    <select name="destination" id="destination">
                        <option value="">All Destinations</option>
                        <?php while ($dest = $destinations_result->fetch_assoc()): ?>
                            <option value="<?php echo $dest['id']; ?>"
                                <?php echo $destination_filter == $dest['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dest['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select name="category" id="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"
                                <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                <?php echo $category; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="items-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($item = $result->fetch_assoc()): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <?php if ($booking_type === 'hotel'): ?>
                                <i class="fas fa-bed"></i>
                            <?php else: ?>
                                <i class="fas fa-map-marked-alt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-content">
                            <h3 class="item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="item-destination">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($item['destination_name']); ?>
                            </div>

                            <div class="item-details">
                                <div class="item-price">
                                    ₹<?php echo number_format($booking_type === 'hotel' ? $item['price_per_night'] : $item['price_per_person']); ?>
                                    <span style="font-size: 0.8rem; color: #666;">
                                        /<?php echo $booking_type === 'hotel' ? 'night' : 'person'; ?>
                                    </span>
                                </div>
                                <?php if ($booking_type === 'package'): ?>
                                    <div class="item-duration">
                                        <i class="fas fa-clock"></i>
                                        <?php echo $item['duration_days']; ?> days
                                    </div>
                                <?php endif; ?>
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
                                <span class="rating-text">
                                    <?php echo number_format($rating, 1); ?> (<?php echo $item['total_reviews']; ?> reviews)
                                </span>
                            </div>

                            <button class="book-btn" onclick="bookItem('<?php echo $booking_type; ?>', <?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>')">
                                <i class="fas fa-calendar-plus"></i>
                                Book Now
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No results found</h3>
                    <p>Try adjusting your filters to see more options.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function bookItem(type, id, name) {
            // Redirect to booking details page
            window.location.href = `booking_details.php?type=${type}&id=${id}`;
        }

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
    </script>
</body>

</html>