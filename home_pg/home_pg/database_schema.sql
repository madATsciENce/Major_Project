-- Enhanced Database Schema for Safar Travel Website
-- Run this script to create/update your database structure

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS project;
USE project;

-- Enhanced Users Table (replacing registration_signup)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    password VARCHAR(255) NOT NULL,
    verification_token VARCHAR(64),
    verified TINYINT(1) DEFAULT 0,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
);

-- User Sessions Table
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(128) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Destinations Table (enhanced)
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    direction ENUM('North', 'South', 'East', 'West', 'Central') NOT NULL,
    state VARCHAR(50),
    description TEXT,
    image_url VARCHAR(255),
    featured_image VARCHAR(255),
    price_per_person DECIMAL(10,2),
    duration_days INT,
    category ENUM('Mountain', 'Beach', 'Wildlife', 'Camping', 'Monuments', 'Adventure') NOT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hotels Table (enhanced)
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    destination_id INT,
    address TEXT,
    description TEXT,
    price_per_night DECIMAL(10,2),
    amenities JSON,
    images JSON,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    total_rooms INT DEFAULT 0,
    available_rooms INT DEFAULT 0,
    contact_phone VARCHAR(15),
    contact_email VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
);

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    destination_id INT,
    description TEXT,
    price_per_person DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    max_participants INT DEFAULT 50,
    includes JSON,
    excludes JSON,
    itinerary JSON,
    images JSON,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_type ENUM('hotel', 'package') NOT NULL,
    hotel_id INT NULL,
    package_id INT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    participants INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_gateway ENUM('razorpay', 'stripe', 'paypal') NOT NULL,
    gateway_payment_id VARCHAR(100),
    gateway_order_id VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'INR',
    payment_status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_fee DECIMAL(10,2) DEFAULT 0.00,
    gateway_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    images JSON,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin') DEFAULT 'admin',
    permissions JSON,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Password Reset Tokens Table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- OTP Verification Table
CREATE TABLE IF NOT EXISTS otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(15) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    purpose ENUM('registration', 'login', 'password_reset') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified TINYINT(1) DEFAULT 0,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample admin user (password: admin123)
INSERT IGNORE INTO admin_users (username, email, password, role) VALUES 
('admin', 'admin@safar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- Insert sample destinations
INSERT IGNORE INTO destinations (name, direction, state, description, price_per_person, duration_days, category) VALUES 
('Rishikesh', 'North', 'Uttarakhand', 'Adventure capital of India with river rafting and spiritual experiences', 2500.00, 3, 'Adventure'),
('Wayanad', 'South', 'Kerala', 'Beautiful hill station with wildlife and spice plantations', 3000.00, 4, 'Wildlife'),
('Darjeeling', 'East', 'West Bengal', 'Queen of Hills with tea gardens and mountain views', 3500.00, 5, 'Mountain'),
('Digha', 'East', 'West Bengal', 'Popular beach destination with golden sands', 2000.00, 2, 'Beach');

-- Insert sample hotels
INSERT IGNORE INTO hotels (name, destination_id, price_per_night, total_rooms, available_rooms) VALUES 
('Rishikesh Resort', 1, 1500.00, 50, 45),
('Wayanad Wildlife Lodge', 2, 2000.00, 30, 25),
('Darjeeling Hill View', 3, 1800.00, 40, 35),
('Digha Beach Resort', 4, 1200.00, 60, 50);

-- Insert sample packages
INSERT IGNORE INTO packages (name, destination_id, price_per_person, duration_days, max_participants) VALUES 
('Rishikesh Adventure Package', 1, 2500.00, 3, 20),
('Wayanad Nature Package', 2, 3000.00, 4, 15),
('Darjeeling Tea Garden Tour', 3, 3500.00, 5, 25),
('Digha Beach Holiday', 4, 2000.00, 2, 30);
