<?php
// razorpay_webhook.php

// Replace with your Razorpay secret webhook key
$webhookSecret = 'YOUR_RAZORPAY_WEBHOOK_SECRET';

// Read the request's body
$body = file_get_contents('php://input');

// Get the signature sent by Razorpay
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Verify the signature
$expectedSignature = hash_hmac('sha256', $body, $webhookSecret);

if (!hash_equals($expectedSignature, $signature)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

// Decode the JSON payload
$payload = json_decode($body, true);

// Connect to database (update with your DB credentials)
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_db_username';
$password = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Handle payment captured event
if ($payload['event'] === 'payment.captured') {
    $paymentId = $payload['payload']['payment']['entity']['id'] ?? null;
    $orderId = $payload['payload']['payment']['entity']['order_id'] ?? null;
    $status = $payload['payload']['payment']['entity']['status'] ?? null;
    $amount = $payload['payload']['payment']['entity']['amount'] ?? 0;
    $email = $payload['payload']['payment']['entity']['email'] ?? '';
    $contact = $payload['payload']['payment']['entity']['contact'] ?? '';

    if ($paymentId && $orderId && $status === 'captured') {
        // Update booking record with payment info
        $stmt = $pdo->prepare("UPDATE bookings SET payment_id = ?, payment_status = ?, amount_paid = ?, payer_email = ?, payer_contact = ? WHERE razorpay_order_id = ?");
        $stmt->execute([$paymentId, $status, $amount / 100, $email, $contact, $orderId]);

        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Unhandled event']);
exit;