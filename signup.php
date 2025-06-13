<?php
require_once 'db.php';  // include your db connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(16)); // creates a 32-character verification token

    // Insert into DB
    $sql = "INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $email, $passwordHash, $token]);

    // Email verification link
    $verifyLink = "http://localhost/project_6thsem/verify.php?token=$token";
    $subject = "Verify your Email";
    $message = "Click the link to verify your email: $verifyLink";
    $headers = "From: your-email@example.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "Verification link sent to your email!";
    } else {
        echo "Failed to send verification email.";
    }
}
