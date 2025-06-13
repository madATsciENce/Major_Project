<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "register") {
    $conn = new mysqli("localhost", "root", "", "project");
    
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }
    
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $age = $_POST["age"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $password = $_POST["password"] ?? "";
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }
    
    if (strlen($phone) != 10) {
        echo json_encode(["success" => false, "message" => "Phone number must be 10 digits"]);
        exit;
    }
    
    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists"]);
        exit;
    }
    
    // Insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, age, gender, password, verification_token, verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssssss", $name, $email, $phone, $age, $gender, $hashed_password, $token);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful! You can now sign in."]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed: " . $stmt->error]);
    }
    
    $conn->close();
    exit;
}
?>