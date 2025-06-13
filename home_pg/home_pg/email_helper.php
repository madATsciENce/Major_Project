<?php
/**
 * Email Helper Functions
 * Note: This is a basic implementation for demonstration.
 * In production, use proper email services like PHPMailer, SendGrid, or similar.
 */

function sendVerificationEmail($email, $name, $verification_token) {
    // In a real application, you would use a proper email service
    // For now, we'll just log the email content or display it
    
    $verification_link = "http://localhost/home_pg/home_pg/verify_email_change.php?token=" . $verification_token;
    
    $subject = "Verify Your New Email Address - Safar";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #3c00a0, #5a2d91); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; background: #3c00a0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Safar - Email Verification</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($name) . ",</h2>
                <p>You have requested to change your email address on Safar. To complete this process, please verify your new email address by clicking the button below:</p>
                
                <p style='text-align: center;'>
                    <a href='" . $verification_link . "' class='button'>Verify Email Address</a>
                </p>
                
                <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px;'>" . $verification_link . "</p>
                
                <p><strong>Important:</strong> This verification link will expire in 24 hours for security reasons.</p>
                
                <p>If you didn't request this email change, please ignore this email or contact our support team.</p>
                
                <p>Best regards,<br>The Safar Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // For demonstration purposes, we'll save the email to a file
    // In production, you would actually send the email
    $email_log = "=== EMAIL VERIFICATION ===\n";
    $email_log .= "To: " . $email . "\n";
    $email_log .= "Subject: " . $subject . "\n";
    $email_log .= "Verification Link: " . $verification_link . "\n";
    $email_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $email_log .= "========================\n\n";
    
    // Save to log file (create if doesn't exist)
    file_put_contents('email_log.txt', $email_log, FILE_APPEND | LOCK_EX);
    
    // For development, also display the verification link
    if (isset($_SESSION)) {
        $_SESSION['verification_link'] = $verification_link;
    }
    
    return true;
}

function sendPasswordResetEmail($email, $name, $reset_token) {
    $reset_link = "http://localhost/home_pg/home_pg/reset_password.php?token=" . $reset_token;
    
    $subject = "Password Reset Request - Safar";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #3c00a0, #5a2d91); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; background: #3c00a0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Safar - Password Reset</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($name) . ",</h2>
                <p>You have requested to reset your password on Safar. Click the button below to reset your password:</p>
                
                <p style='text-align: center;'>
                    <a href='" . $reset_link . "' class='button'>Reset Password</a>
                </p>
                
                <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px;'>" . $reset_link . "</p>
                
                <p><strong>Important:</strong> This password reset link will expire in 1 hour for security reasons.</p>
                
                <p>If you didn't request this password reset, please ignore this email or contact our support team.</p>
                
                <p>Best regards,<br>The Safar Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Log the email
    $email_log = "=== PASSWORD RESET ===\n";
    $email_log .= "To: " . $email . "\n";
    $email_log .= "Subject: " . $subject . "\n";
    $email_log .= "Reset Link: " . $reset_link . "\n";
    $email_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $email_log .= "======================\n\n";
    
    file_put_contents('email_log.txt', $email_log, FILE_APPEND | LOCK_EX);
    
    return true;
}

function sendWelcomeEmail($email, $name) {
    $subject = "Welcome to Safar - Your Journey Begins!";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #3c00a0, #5a2d91); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; background: #3c00a0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to Safar!</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($name) . ",</h2>
                <p>Welcome to Safar! We're excited to have you join our travel community.</p>
                
                <p>With Safar, you can:</p>
                <ul>
                    <li>Explore amazing destinations across India</li>
                    <li>Book hotels and travel packages</li>
                    <li>Manage your bookings and travel history</li>
                    <li>Get personalized travel recommendations</li>
                </ul>
                
                <p style='text-align: center;'>
                    <a href='http://localhost/home_pg/home_pg/home3.html' class='button'>Start Exploring</a>
                </p>
                
                <p>If you have any questions, feel free to contact our support team.</p>
                
                <p>Happy travels!<br>The Safar Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Log the email
    $email_log = "=== WELCOME EMAIL ===\n";
    $email_log .= "To: " . $email . "\n";
    $email_log .= "Subject: " . $subject . "\n";
    $email_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $email_log .= "=====================\n\n";
    
    file_put_contents('email_log.txt', $email_log, FILE_APPEND | LOCK_EX);
    
    return true;
}
?>
