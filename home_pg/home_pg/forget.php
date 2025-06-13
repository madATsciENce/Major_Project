<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);

// Error check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$step = 1;
$email = "";
$error = "";
$success = "";

// Step 1: Check Email
if (isset($_POST['check_email'])) {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT * FROM registration_signup WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $step = 2; // Show reset form
    } else {
        $error = "Email not found.";
    }
}

// Step 2: Handle Password Reset
if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validation
    if (empty($password) || empty($confirm)) {
        $error = "Both password fields are required.";
        $step = 2;
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
        $step = 2;
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
        $step = 2;
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE registration_signup SET Password = ? WHERE Email = ?");
        $stmt->bind_param("ss", $hashed, $email);
        if ($stmt->execute()) {
            $success = "Password reset successfully.";
            $step = 1;
        } else {
            $error = "Error updating password.";
            $step = 2;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forget Password</title>
    <style>
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Forget Password</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if ($step === 1): ?>
        <form method="post" action="">
            Email: <input type="email" name="email" required><br><br>
            <button type="submit" name="check_email">Check Email</button>
        </form>
    <?php elseif ($step === 2): ?>
        <form method="post" action="">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            New Password: <input type="password" name="password" required><br><br>
            Confirm Password: <input type="password" name="confirm_password" required><br><br>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>

</tr>
<tr>
<td><center><input type="submit" name="Submit" value="Submit"></center></td>
</tr>
</table>
</form>
<?php
if (isset($_GET['Submit'])) {
    $a = $_GET['Area'];
    if ($a == "Rishikesh" || $a == "Wayanad") {
        echo "<center><h3>Valid Area: $a</h3></center>";
echo $aa="insert into destination values('$a')";
$bb= mysqli_query($conn,$aa);

    } else {
        echo "<center><h3>Only Rishikesh and Wayanad are allowed.</h3></center>";
    }
}
?>