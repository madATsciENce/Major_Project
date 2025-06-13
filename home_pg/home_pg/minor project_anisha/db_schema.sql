-- SQL schema for bookings table

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    guests INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    razorpay_order_id VARCHAR(255) DEFAULT NULL,
    payment_id VARCHAR(255) DEFAULT NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    amount_paid DECIMAL(10,2) DEFAULT 0,
    payer_email VARCHAR(255) DEFAULT NULL,
    payer_contact VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
