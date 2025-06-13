<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['Phone_Number'];

    // Validate phone number length
    if (strlen($phone) != 10) {
        die("Phone number must be exactly 10 digits.");
    }

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    // Store OTP and expiry in session or database
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = $expiry;
    $_SESSION['otp_phone'] = $phone;

    // TODO: Integrate with SMS API like Twilio to send OTP
    // For now, simulate sending OTP by email or display
    // mail or SMS sending code here

    echo "OTP sent to phone number: $phone. OTP is $otp (for testing only).";
} else {
    echo "Invalid request method.";
}
?>