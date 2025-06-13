<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthHandler
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "project");
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            throw new Exception("Database connection failed");
        }
    }

    // Register new user
    public function register($name, $email, $phone, $age, $gender, $password)
    {
        try {
            // Validate input
            if (strlen($phone) != 10) {
                return ['success' => false, 'message' => 'Phone number must be exactly 10 digits.'];
            }

            if (strpos($email, '@') === false) {
                return ['success' => false, 'message' => 'Please enter a valid email address.'];
            }

            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return ['success' => false, 'message' => 'Email already exists. Please use a different email.'];
            }

            // Hash password and generate verification token
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(32));

            // Insert user
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone_number, age, gender, password, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $email, $phone, $age, $gender, $hashed_password, $verification_token);

            if ($stmt->execute()) {
                // Send verification email (simplified for now)
                $this->sendVerificationEmail($email, $verification_token);
                return ['success' => true, 'message' => 'Registration successful! Please check your email to verify your account.'];
            } else {
                return ['success' => false, 'message' => 'Registration failed. Please try again.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($email, $password, $remember_me = false)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, email, password, verified FROM users WHERE email = ? AND status = 'active'");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return ['success' => false, 'message' => 'Invalid email or password.'];
            }

            $user = $result->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid email or password.'];
            }

            if (!$user['verified']) {
                return ['success' => false, 'message' => 'Please verify your email before logging in.'];
            }

            // Create session
            $this->createUserSession($user['id'], $remember_me);

            // Update last login
            $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();

            return ['success' => true, 'message' => 'Login successful!', 'user' => $user];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    // Create user session
    private function createUserSession($user_id, $remember_me = false)
    {
        $session_token = bin2hex(random_bytes(32));
        $expires_at = $remember_me ? date('Y-m-d H:i:s', strtotime('+30 days')) : date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Store in database
        $stmt = $this->conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt->bind_param("issss", $user_id, $session_token, $expires_at, $ip_address, $user_agent);
        $stmt->execute();

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['session_token'] = $session_token;
        $_SESSION['logged_in'] = true;

        // Set cookie if remember me
        if ($remember_me) {
            setcookie('remember_token', $session_token, strtotime('+30 days'), '/', '', false, true);
        }
    }

    // Check if user is logged in
    public function isLoggedIn()
    {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return $this->validateSession($_SESSION['session_token']);
        }

        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            return $this->validateSession($_COOKIE['remember_token']);
        }

        return false;
    }

    // Validate session token
    private function validateSession($token)
    {
        $stmt = $this->conn->prepare("SELECT us.user_id, u.name, u.email FROM user_sessions us JOIN users u ON us.user_id = u.id WHERE us.session_token = ? AND us.expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $session = $result->fetch_assoc();
            $_SESSION['user_id'] = $session['user_id'];
            $_SESSION['user_name'] = $session['name'];
            $_SESSION['user_email'] = $session['email'];
            $_SESSION['logged_in'] = true;
            return true;
        }

        return false;
    }

    // Get current user info
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ];
        }
        return null;
    }

    // Logout user
    public function logout()
    {
        if (isset($_SESSION['session_token'])) {
            // Remove session from database
            $stmt = $this->conn->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->bind_param("s", $_SESSION['session_token']);
            $stmt->execute();
        }

        // Clear session
        session_destroy();

        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        return ['success' => true, 'message' => 'Logged out successfully.'];
    }

    // Verify email
    public function verifyEmail($token)
    {
        $stmt = $this->conn->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?");
        $stmt->bind_param("s", $token);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return ['success' => true, 'message' => 'Email verified successfully! You can now log in.'];
        } else {
            return ['success' => false, 'message' => 'Invalid or expired verification token.'];
        }
    }

    // Send verification email (simplified)
    private function sendVerificationEmail($email, $token)
    {
        $verification_link = "http://localhost/project_6thsem/home_pg/home_pg/verify_email.php?token=" . $token;
        $subject = "Verify Your Email - Safar";
        $message = "Please click the following link to verify your email: " . $verification_link;
        $headers = "From: no-reply@safar.com";

        // In production, use a proper email service
        mail($email, $subject, $message, $headers);
    }

    // Request password reset
    public function requestPasswordReset($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Email not found.'];
        }

        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store reset token
        $stmt = $this->conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires_at);
        $stmt->execute();

        // Send reset email
        $reset_link = "http://localhost/project_6thsem/home_pg/home_pg/reset_password.php?token=" . $token;
        $subject = "Password Reset - Safar";
        $message = "Click the following link to reset your password: " . $reset_link;
        $headers = "From: no-reply@safar.com";

        mail($email, $subject, $message, $headers);

        return ['success' => true, 'message' => 'Password reset link sent to your email.'];
    }

    // Reset password
    public function resetPassword($token, $new_password)
    {
        $stmt = $this->conn->prepare("SELECT user_id FROM password_reset_tokens WHERE token = ? AND expires_at > NOW() AND used = 0");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid or expired reset token.'];
        }

        $reset_data = $result->fetch_assoc();
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $reset_data['user_id']);
        $stmt->execute();

        // Mark token as used
        $stmt = $this->conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        return ['success' => true, 'message' => 'Password reset successfully.'];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    try {
        $auth = new AuthHandler();

        switch ($_POST['action']) {
            case 'register':
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $age = $_POST['age'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($name) || empty($email) || empty($phone) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    break;
                }

                $result = $auth->register($name, $email, $phone, $age, $gender, $password);
                echo json_encode($result);
                break;

            case 'login':
                $remember = isset($_POST['remember']) ? true : false;
                $result = $auth->login($_POST['email'], $_POST['password'], $remember);
                echo json_encode($result);
                break;

            case 'logout':
                $result = $auth->logout();
                echo json_encode($result);
                break;

            case 'check_login':
                $user = $auth->getCurrentUser();
                echo json_encode(['logged_in' => $user !== null, 'user' => $user]);
                break;

            case 'request_reset':
                $result = $auth->requestPasswordReset($_POST['email']);
                echo json_encode($result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        error_log("Auth Handler Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
    }
    exit;
}
