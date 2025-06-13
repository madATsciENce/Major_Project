<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Request</title>
</head>

<body>
    <h2>Reset Your Password</h2>
    <form action="password_reset_request.php" method="post">
        <label for="email">Enter your email address:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" name="reset_request" value="Send Reset Link">
    </form>

    <?php
    if (isset($_POST['reset_request'])) {
        $email = $_POST['email'];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "project";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email exists in users or admins
        $user_check = $conn->prepare("SELECT Email FROM registration_signup WHERE Email = ?");
        $user_check->bind_param("s", $email);
        $user_check->execute();
        $user_result = $user_check->get_result();

        $admin_check = $conn->prepare("SELECT Email FROM admin_users WHERE Email = ?");
        $admin_check->bind_param("s", $email);
        $admin_check->execute();
        $admin_result = $admin_check->get_result();

        if ($user_result->num_rows > 0 || $admin_result->num_rows > 0) {
            // Generate reset token and expiration (1 hour)
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Store token and expiration in a password_resets table
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token=?, expires=?");
            $stmt->bind_param("sssss", $email, $token, $expires, $token, $expires);
            $stmt->execute();

            // Send reset email
            $reset_link = "http://yourdomain.com/project/home_pg/home_pg/password_reset.php?token=" . $token;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: " . $reset_link;
            $headers = "From: no-reply@yourdomain.com";

            mail($email, $subject, $message, $headers);

            echo "A password reset link has been sent to your email.";
        } else {
            echo "Email address not found.";
        }

        $conn->close();
    }
    ?>
</body>

</html>