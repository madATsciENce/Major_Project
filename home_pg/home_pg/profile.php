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

$user_id = $user['id'];
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $age = intval($_POST['age']);
        $gender = $_POST['gender'];

        // Validation
        if (empty($name)) {
            $message = "Name is required.";
            $message_type = 'error';
        } elseif (strlen($phone) != 10 || !ctype_digit($phone)) {
            $message = "Phone number must be exactly 10 digits.";
            $message_type = 'error';
        } elseif ($age < 1 || $age > 120) {
            $message = "Please enter a valid age.";
            $message_type = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, phone_number=?, age=?, gender=? WHERE id=?");
            $stmt->bind_param("ssisi", $name, $phone, $age, $gender, $user_id);
            if ($stmt->execute()) {
                $message = "Profile updated successfully!";
                $message_type = 'success';
                // Update session data
                $_SESSION['user_name'] = $name;
            } else {
                $message = "Error updating profile.";
                $message_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($action === 'change_email') {
        $new_email = trim($_POST['new_email']);
        $password = $_POST['current_password'];

        if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address.";
            $message_type = 'error';
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();

            if (password_verify($password, $user_data['password'])) {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $new_email, $user_id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $message = "Email already exists. Please use a different email.";
                    $message_type = 'error';
                } else {
                    // Generate verification token
                    $verification_token = bin2hex(random_bytes(32));

                    // Store pending email change
                    $stmt = $conn->prepare("UPDATE users SET email = ?, verification_token = ?, verified = 0 WHERE id = ?");
                    $stmt->bind_param("ssi", $new_email, $verification_token, $user_id);

                    if ($stmt->execute()) {
                        // Send verification email
                        require_once 'email_helper.php';
                        sendVerificationEmail($new_email, $user_data['name'], $verification_token);

                        $message = "Email change requested. Please check your new email (" . $new_email . ") for verification link. <a href='view_emails.php' style='color: #3c00a0;'>View Email Log (Dev)</a>";
                        $message_type = 'info';
                    } else {
                        $message = "Error updating email.";
                        $message_type = 'error';
                    }
                }
            } else {
                $message = "Current password is incorrect.";
                $message_type = 'error';
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "New passwords do not match.";
            $message_type = 'error';
        } elseif (strlen($new_password) < 6) {
            $message = "New password must be at least 6 characters long.";
            $message_type = 'error';
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();

            if (password_verify($current_password, $user_data['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);

                if ($stmt->execute()) {
                    $message = "Password changed successfully!";
                    $message_type = 'success';
                } else {
                    $message = "Error changing password.";
                    $message_type = 'error';
                }
            } else {
                $message = "Current password is incorrect.";
                $message_type = 'error';
            }
        }
    }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email, phone_number, age, gender, profile_image, created_at, last_login FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Safar</title>
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

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
            color: white;
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .profile-name {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .profile-email {
            color: #666;
            font-size: 1.1rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3c00a0;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .profile-section {
            background: white;
            border-radius: 20px;
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
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: #3c00a0;
            background: white;
            box-shadow: 0 0 0 3px rgba(60, 0, 160, 0.1);
        }

        .form-input:disabled {
            background: #e9ecef;
            color: #6c757d;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
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

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .tabs {
            display: flex;
            margin-bottom: 2rem;
            background: white;
            border-radius: 15px;
            padding: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .tab.active {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }

            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .profile-stats {
                grid-template-columns: repeat(2, 1fr);
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
                <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="booking_system.php"><i class="fas fa-calendar-plus"></i> Book Now</a>
                <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($user_data['name']); ?></h1>
            <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user_data['age']; ?></div>
                    <div class="stat-label">Years Old</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo ucfirst($user_data['gender']); ?></div>
                    <div class="stat-label">Gender</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('M Y', strtotime($user_data['created_at'])); ?></div>
                    <div class="stat-label">Member Since</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user_data['last_login'] ? date('M d', strtotime($user_data['last_login'])) : 'Never'; ?></div>
                    <div class="stat-label">Last Login</div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('profile')">
                <i class="fas fa-user"></i> Profile Info
            </div>
            <div class="tab" onclick="showTab('email')">
                <i class="fas fa-envelope"></i> Change Email
            </div>
            <div class="tab" onclick="showTab('password')">
                <i class="fas fa-lock"></i> Change Password
            </div>
        </div>

        <!-- Tab Contents -->
        <div id="profile-tab" class="tab-content active">
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-user-edit"></i>
                    Personal Information
                </h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input"
                            value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input"
                            value="<?php echo htmlspecialchars($user_data['phone_number']); ?>"
                            pattern="[0-9]{10}" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="age">Age</label>
                        <input type="number" id="age" name="age" class="form-input"
                            value="<?php echo htmlspecialchars($user_data['age']); ?>"
                            min="1" max="120" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-input" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo $user_data['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $user_data['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $user_data['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>

        <div id="email-tab" class="tab-content">
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i>
                    Change Email Address
                </h2>
                <p style="color: #666; margin-bottom: 1.5rem;">
                    <i class="fas fa-info-circle"></i>
                    Changing your email will require verification. You'll need to verify the new email before you can use it to log in.
                </p>
                <form method="post">
                    <input type="hidden" name="action" value="change_email">

                    <div class="form-group">
                        <label class="form-label" for="current_email">Current Email</label>
                        <input type="email" id="current_email" class="form-input"
                            value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_email">New Email Address</label>
                        <input type="email" id="new_email" name="new_email" class="form-input"
                            placeholder="Enter your new email address" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="current_password_email">Current Password</label>
                        <input type="password" id="current_password_email" name="current_password" class="form-input"
                            placeholder="Enter your current password to confirm" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Change Email
                    </button>
                </form>
            </div>
        </div>

        <div id="password-tab" class="tab-content">
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-lock"></i>
                    Change Password
                </h2>
                <form method="post">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label class="form-label" for="current_password_pwd">Current Password</label>
                        <input type="password" id="current_password_pwd" name="current_password" class="form-input"
                            placeholder="Enter your current password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-input"
                            placeholder="Enter your new password (min 6 characters)" minlength="6" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                            placeholder="Confirm your new password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active class to clicked tab
            event.target.closest('.tab').classList.add('active');
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;

            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
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
    </script>
</body>

</html>