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

// Get user's booking statistics
$user_id = $user['id'];
$stats_query = "SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN booking_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN booking_status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
    SUM(total_amount) as total_spent
    FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get recent bookings
$recent_bookings_query = "SELECT b.*, 
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
    ORDER BY b.created_at DESC
    LIMIT 5";
$stmt = $conn->prepare($recent_bookings_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Safar</title>
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

        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .welcome-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-card:nth-child(1) .stat-icon {
            color: #4CAF50;
        }

        .stat-card:nth-child(2) .stat-icon {
            color: #2196F3;
        }

        .stat-card:nth-child(3) .stat-icon {
            color: #FF9800;
        }

        .stat-card:nth-child(4) .stat-icon {
            color: #9C27B0;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
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

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .bookings-table th,
        .bookings-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .bookings-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
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

        .no-bookings {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .no-bookings i {
            font-size: 3rem;
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

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .bookings-table {
                font-size: 0.9rem;
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
                <a href="booking_system.php"><i class="fas fa-calendar-plus"></i> Book Now</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p class="welcome-subtitle">Here's an overview of your travel activities</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?php echo $stats['total_bookings'] ?? 0; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?php echo $stats['confirmed_bookings'] ?? 0; ?></div>
                <div class="stat-label">Confirmed Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><?php echo $stats['pending_bookings'] ?? 0; ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-number">₹<?php echo number_format($stats['total_spent'] ?? 0); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            <div class="quick-actions">
                <a href="booking_system.php?type=package" class="action-btn">
                    <i class="fas fa-map-marked-alt"></i>
                    Book Package
                </a>
                <a href="booking_system.php?type=hotel" class="action-btn">
                    <i class="fas fa-bed"></i>
                    Book Hotel
                </a>
                <a href="booking_history.php" class="action-btn">
                    <i class="fas fa-history"></i>
                    View All Bookings
                </a>
                <a href="profile.php" class="action-btn">
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </a>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-clock"></i>
                Recent Bookings
            </h2>
            <?php if ($recent_bookings->num_rows > 0): ?>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['booking_name']); ?></strong><br>
                                    <small><?php echo ucfirst($booking['booking_type']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['destination_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                <td>₹<?php echo number_format($booking['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                        <?php echo ucfirst($booking['booking_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No bookings yet</h3>
                    <p>Start your journey by booking your first trip!</p>
                    <a href="booking_system.php" class="action-btn" style="display: inline-flex; margin-top: 1rem;">
                        <i class="fas fa-plus"></i>
                        Make Your First Booking
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
    </script>
</body>

</html>