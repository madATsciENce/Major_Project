<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body>
    <h2>Set New Password</h2>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        // Check if token is valid and not expired
        $stmt = $conn->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $expires = $row['expires'];
            $email = $row['email'];

            if (strtotime($expires) < time()) {
                echo "Reset link has expired.";
                exit;
            }

            if (isset($_POST['reset_password'])) {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                if ($new_password !== $confirm_password) {
                    echo "Passwords do not match.";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password in users table if exists
                    $update_user = $conn->prepare("UPDATE registration_signup SET Password = ? WHERE Email = ?");
                    $update_user->bind_param("ss", $hashed_password, $email);
                    $update_user->execute();

                    // Update password in admin_users table if exists
                    $update_admin = $conn->prepare("UPDATE admin_users SET Password = ? WHERE Email = ?");
                    $update_admin->bind_param("ss", $hashed_password, $email);
                    $update_admin->execute();

                    // Delete the token after successful reset
                    $delete_token = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $delete_token->bind_param("s", $token);
                    $delete_token->execute();

                    echo "Password has been reset successfully. You can now <a href='signin.php'>login</a>.";
                    exit;
                }
            }
            ?>
    <form action="" method="post">
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" name="reset_password" value="Reset Password">
    </form>
    <?php
        } else {
            echo "Invalid reset token.";
        }
    } else {
        echo "No reset token provided.";
    }

    $conn->close();
    ?>
</body>

</html>