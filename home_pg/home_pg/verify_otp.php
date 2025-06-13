<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];

    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry'])) {
        die("No OTP session found. Please request a new OTP.");
    }

    $current_time = date("Y-m-d H:i:s");
    if ($current_time > $_SESSION['otp_expiry']) {
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        unset($_SESSION['otp_phone']);
        die("OTP expired. Please request a new OTP.");
    }

    if ($input_otp == $_SESSION['otp']) {
        // OTP verified successfully
        // Proceed with login or registration completion
        echo "OTP verified successfully.";

        // Clear OTP session
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        unset($_SESSION['otp_phone']);

        // Set user session or other login logic here
    } else {
        echo "Invalid OTP. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
