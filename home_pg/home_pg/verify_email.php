<?php
require_once 'auth_handler.php';

$auth = new AuthHandler();
$token = $_GET['token'] ?? '';
$message = '';
$success = false;

if ($token) {
    $result = $auth->verifyEmail($token);
    $message = $result['message'];
    $success = $result['success'];
} else {
    $message = 'Invalid verification link.';
}
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
        }

        .verification-container {
            background: white;
            border-radius: 20px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        .verification-header {
            padding: 3rem 2rem;
            position: relative;
        }

        .verification-header.success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .verification-header.error {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
        }

        .verification-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .verification-title {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .verification-message {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .verification-content {
            padding: 2rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
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
            background: #f8f9fa;
            color: #333;
            border: 2px solid #eee;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #ddd;
        }

        .additional-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .additional-info h4 {
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .additional-info ul {
            color: #666;
            padding-left: 1.5rem;
        }

        .additional-info li {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .verification-container {
                width: 95%;
                margin: 1rem;
            }

            .verification-header {
                padding: 2rem 1rem;
            }

            .verification-content {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="verification-container">
        <div class="verification-header <?php echo $success ? 'success' : 'error'; ?>">
            <div class="verification-icon">
                <?php if ($success): ?>
                    <i class="fas fa-check"></i>
                <?php else: ?>
                    <i class="fas fa-times"></i>
                <?php endif; ?>
            </div>
            <h1 class="verification-title">
                <?php echo $success ? 'Email Verified!' : 'Verification Failed'; ?>
            </h1>
            <p class="verification-message">
                <?php echo htmlspecialchars($message); ?>
            </p>
        </div>

        <div class="verification-content">
            <?php if ($success): ?>
                <div class="additional-info">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        What's Next?
                    </h4>
                    <ul>
                        <li>Your account is now fully activated</li>
                        <li>You can now sign in and start booking</li>
                        <li>Explore our amazing travel packages and hotels</li>
                        <li>Enjoy exclusive member benefits and discounts</li>
                    </ul>
                </div>

                <div class="action-buttons">
                    <a href="home3.html" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Go to Homepage
                    </a>
                    <a href="home3.html" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In Now
                    </a>
                </div>
            <?php else: ?>
                <div class="additional-info">
                    <h4>
                        <i class="fas fa-exclamation-triangle"></i>
                        Possible Reasons
                    </h4>
                    <ul>
                        <li>The verification link has expired</li>
                        <li>The link has already been used</li>
                        <li>The link is invalid or corrupted</li>
                        <li>Your email has already been verified</li>
                    </ul>
                </div>

                <div class="action-buttons">
                    <a href="home3.html" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Go to Homepage
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-redirect to homepage after successful verification
        <?php if ($success): ?>
            setTimeout(() => {
                if (confirm('Verification successful! Would you like to go to the homepage now?')) {
                    window.location.href = 'home3.html';
                }
            }, 5000);
        <?php endif; ?>
    </script>
</body>

</html>