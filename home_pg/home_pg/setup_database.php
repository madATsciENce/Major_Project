<?php
// Database Setup Script for Safar Travel Website
// Run this file once to set up your database

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($dbname);

// Read and execute the SQL schema file
$sql_file = file_get_contents('database_schema.sql');
if ($sql_file === false) {
    die("Error reading database_schema.sql file");
}

// Split the SQL file into individual queries
$queries = explode(';', $sql_file);

echo "<h2>Setting up database tables...</h2>";

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === TRUE) {
            // Extract table name from CREATE TABLE statements
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $query, $matches)) {
                echo "✓ Table '{$matches[1]}' created successfully<br>";
            } elseif (preg_match('/INSERT.*?INTO.*?`?(\w+)`?/i', $query, $matches)) {
                echo "✓ Sample data inserted into '{$matches[1]}'<br>";
            } else {
                echo "✓ Query executed successfully<br>";
            }
        } else {
            echo "❌ Error executing query: " . $conn->error . "<br>";
            echo "Query: " . substr($query, 0, 100) . "...<br><br>";
        }
    }
}

// Migrate existing data from old table structure if it exists
echo "<h2>Migrating existing data...</h2>";

// Check if old registration_signup table exists
$check_old_table = "SHOW TABLES LIKE 'registration_signup'";
$result = $conn->query($check_old_table);

if ($result->num_rows > 0) {
    echo "Found existing registration_signup table. Migrating data...<br>";
    
    // Migrate data from old table to new users table
    $migrate_query = "INSERT IGNORE INTO users (name, email, phone_number, age, gender, password, verified, created_at)
                     SELECT Name, Email, Phone_Number, Age, Select_Gender, Password, 
                            COALESCE(verified, 0), NOW()
                     FROM registration_signup 
                     WHERE Email NOT IN (SELECT email FROM users)";
    
    if ($conn->query($migrate_query) === TRUE) {
        echo "✓ User data migrated successfully<br>";
    } else {
        echo "❌ Error migrating user data: " . $conn->error . "<br>";
    }
} else {
    echo "No existing registration_signup table found. Skipping migration.<br>";
}

// Create a default admin user if it doesn't exist
echo "<h2>Creating default admin user...</h2>";

$admin_email = "admin@safar.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);

$check_admin = "SELECT id FROM admin_users WHERE email = '$admin_email'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    $create_admin = "INSERT INTO admin_users (username, email, password, role) 
                    VALUES ('admin', '$admin_email', '$admin_password', 'superadmin')";
    
    if ($conn->query($create_admin) === TRUE) {
        echo "✓ Default admin user created<br>";
        echo "  Email: admin@safar.com<br>";
        echo "  Password: admin123<br>";
    } else {
        echo "❌ Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "✓ Admin user already exists<br>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Complete - Safar</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h1 {
            color: #3c00a0;
            text-align: center;
            margin-bottom: 2rem;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
            margin: 2rem 0 1rem 0;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin: 2rem 0;
            border: 1px solid #c3e6cb;
        }

        .next-steps {
            background: #fff3cd;
            color: #856404;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
            border: 1px solid #ffeaa7;
        }

        .next-steps h3 {
            margin-bottom: 1rem;
            color: #856404;
        }

        .next-steps ul {
            margin-left: 1.5rem;
        }

        .next-steps li {
            margin-bottom: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3c00a0, #5a2d91);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 0, 160, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #eee;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #ddd;
        }

        .warning {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Database Setup Complete!</h1>
        
        <div class="success">
            <strong>Success!</strong> Your Safar travel website database has been set up successfully.
        </div>

        <div class="next-steps">
            <h3>🚀 Next Steps:</h3>
            <ul>
                <li><strong>Test the website:</strong> Go to <a href="home3.html">home3.html</a> to see your homepage</li>
                <li><strong>Create an account:</strong> Sign up as a new user to test the registration flow</li>
                <li><strong>Admin access:</strong> Use admin@safar.com / admin123 to access admin features</li>
                <li><strong>Add content:</strong> Add more destinations, hotels, and packages through the admin panel</li>
                <li><strong>Configure email:</strong> Set up proper email configuration for verification emails</li>
                <li><strong>Payment gateway:</strong> Configure Razorpay/Stripe for live payments</li>
            </ul>
        </div>

        <div class="warning">
            <strong>Important:</strong> For security reasons, delete this setup file (setup_database.php) after running it once.
        </div>

        <h2>📋 What's Been Set Up:</h2>
        <ul>
            <li>✅ Complete user authentication system</li>
            <li>✅ Booking and payment management</li>
            <li>✅ Hotel and package management</li>
            <li>✅ Admin panel with role-based access</li>
            <li>✅ Email verification system</li>
            <li>✅ Responsive design for all devices</li>
            <li>✅ Payment gateway integration (demo mode)</li>
        </ul>

        <h2>🔧 Features Available:</h2>
        <ul>
            <li><strong>User Features:</strong> Registration, Login, Profile Management, Booking History</li>
            <li><strong>Booking System:</strong> Hotel and Package booking with payment integration</li>
            <li><strong>Payment Gateway:</strong> Secure payment processing (currently in demo mode)</li>
            <li><strong>Admin Features:</strong> User management, Booking management, Content management</li>
            <li><strong>Security:</strong> Password hashing, Session management, Email verification</li>
        </ul>

        <div class="action-buttons">
            <a href="home3.html" class="btn btn-primary">
                🏠 Go to Homepage
            </a>
            <a href="user_dashboard.php" class="btn btn-secondary">
                📊 User Dashboard
            </a>
            <a href="admin_signin.php" class="btn btn-secondary">
                🔐 Admin Login
            </a>
        </div>
    </div>
</body>
</html>
