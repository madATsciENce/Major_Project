<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Find user with this verification token
    $stmt = $conn->prepare("SELECT id, email, name FROM users WHERE verification_token = ? AND verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the email
        $stmt = $conn->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        
        if ($stmt->execute()) {
            $message = "Email verified successfully! You can now use your new email address to log in.";
            $message_type = 'success';
            
            // Update session if this is the current user
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) {
                $_SESSION['user_email'] = $user['email'];
            }
        } else {
            $message = "Error verifying email. Please try again.";
            $message_type = 'error';
        }
    } else {
        $message = "Invalid or expired verification token.";
        $message_type = 'error';
    }
} else {
    $message = "No verification token provided.";
    $message_type = 'error';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Safar</title>
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
            padding: 20px;
        }

        .verification-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .verification-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            color: white;
        }

        .success-icon {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .error-icon {
            background: linear-gradient(135deg, #f44336, #da190b);
        }

        .verification-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .verification-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            margin-left: 1rem;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .verification-container {
                padding: 2rem;
            }
            
            .verification-title {
                font-size: 1.5rem;
            }
            
            .btn {
                display: block;
                margin: 0.5rem 0;
                text-align: center;
            }
            
            .btn-secondary {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-icon <?php echo $message_type === 'success' ? 'success-icon' : 'error-icon'; ?>">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check' : 'times'; ?>"></i>
        </div>
        
        <h1 class="verification-title">
            <?php echo $message_type === 'success' ? 'Email Verified!' : 'Verification Failed'; ?>
        </h1>
        
        <p class="verification-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
        
        <div>
            <?php if ($message_type === 'success'): ?>
                <a href="profile.php" class="btn">
                    <i class="fas fa-user"></i> Go to Profile
                </a>
                <a href="home3.html" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Go to Home
                </a>
            <?php else: ?>
                <a href="profile.php" class="btn">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
                <a href="home3.html" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Go to Home
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
