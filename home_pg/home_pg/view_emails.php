<?php
/**
 * Email Viewer for Development
 * This page shows the emails that would be sent in a real application
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
require_once 'auth_handler.php';
$auth = new AuthHandler();
if (!$auth->isLoggedIn()) {
    header("Location: home3.html");
    exit();
}

$email_log_file = 'email_log.txt';
$emails = [];

if (file_exists($email_log_file)) {
    $log_content = file_get_contents($email_log_file);
    $email_blocks = explode('===', $log_content);
    
    foreach ($email_blocks as $block) {
        if (trim($block)) {
            $lines = explode("\n", trim($block));
            $email = [];
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $email[trim($key)] = trim($value);
                }
            }
            if (!empty($email)) {
                $emails[] = $email;
            }
        }
    }
    
    // Reverse to show newest first
    $emails = array_reverse($emails);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Log - Safar (Development)</title>
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

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }

        .header h1 {
            color: #3c00a0;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #666;
        }

        .dev-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .email-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .email-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .email-type {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .email-verification {
            background: #d1ecf1;
            color: #0c5460;
        }

        .password-reset {
            background: #f8d7da;
            color: #721c24;
        }

        .welcome-email {
            background: #d4edda;
            color: #155724;
        }

        .email-time {
            color: #666;
            font-size: 0.9rem;
        }

        .email-details {
            margin-bottom: 1rem;
        }

        .email-detail {
            margin-bottom: 0.5rem;
        }

        .email-detail strong {
            color: #333;
            display: inline-block;
            width: 80px;
        }

        .verification-link {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            border-left: 4px solid #3c00a0;
            margin-top: 1rem;
        }

        .verification-link a {
            color: #3c00a0;
            text-decoration: none;
            word-break: break-all;
        }

        .verification-link a:hover {
            text-decoration: underline;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
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
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(60, 0, 160, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-danger {
            background: #dc3545;
        }

        .no-emails {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-emails i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ddd;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .email-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-envelope"></i> Email Log</h1>
            <p>Development tool to view emails that would be sent in production</p>
        </div>

        <div class="dev-notice">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Development Mode:</strong> In production, these emails would be sent to users' actual email addresses. This page is for testing purposes only.
        </div>

        <div class="actions">
            <a href="profile.php" class="btn">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
            <a href="?clear=1" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all email logs?')">
                <i class="fas fa-trash"></i> Clear Log
            </a>
        </div>

        <?php if (isset($_GET['clear']) && $_GET['clear'] == '1'): ?>
            <?php 
            if (file_exists($email_log_file)) {
                unlink($email_log_file);
            }
            ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                <i class="fas fa-check-circle"></i> Email log cleared successfully!
            </div>
            <?php $emails = []; ?>
        <?php endif; ?>

        <?php if (empty($emails)): ?>
            <div class="email-card">
                <div class="no-emails">
                    <i class="fas fa-inbox"></i>
                    <h3>No emails logged yet</h3>
                    <p>When you request email changes or password resets, they will appear here.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($emails as $email): ?>
                <div class="email-card">
                    <div class="email-header">
                        <div>
                            <?php if (isset($email['EMAIL VERIFICATION'])): ?>
                                <span class="email-type email-verification">Email Verification</span>
                            <?php elseif (isset($email['PASSWORD RESET'])): ?>
                                <span class="email-type password-reset">Password Reset</span>
                            <?php elseif (isset($email['WELCOME EMAIL'])): ?>
                                <span class="email-type welcome-email">Welcome Email</span>
                            <?php endif; ?>
                        </div>
                        <div class="email-time">
                            <?php echo isset($email['Time']) ? $email['Time'] : 'Unknown time'; ?>
                        </div>
                    </div>

                    <div class="email-details">
                        <?php if (isset($email['To'])): ?>
                            <div class="email-detail">
                                <strong>To:</strong> <?php echo htmlspecialchars($email['To']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($email['Subject'])): ?>
                            <div class="email-detail">
                                <strong>Subject:</strong> <?php echo htmlspecialchars($email['Subject']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($email['Verification Link'])): ?>
                        <div class="verification-link">
                            <strong>Verification Link:</strong><br>
                            <a href="<?php echo htmlspecialchars($email['Verification Link']); ?>" target="_blank">
                                <?php echo htmlspecialchars($email['Verification Link']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($email['Reset Link'])): ?>
                        <div class="verification-link">
                            <strong>Reset Link:</strong><br>
                            <a href="<?php echo htmlspecialchars($email['Reset Link']); ?>" target="_blank">
                                <?php echo htmlspecialchars($email['Reset Link']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
