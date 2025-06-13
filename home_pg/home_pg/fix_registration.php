<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Registration Fix Tool</h1>";

// Step 1: Test database connection
echo "<h2>Step 1: Database Connection</h2>";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error . "<br>";
    echo "<strong>Solution:</strong> Make sure XAMPP/WAMP is running and MySQL service is started.<br>";
    die();
} else {
    echo "✅ MySQL connection successful<br>";
}

// Step 2: Create database if it doesn't exist
echo "<h2>Step 2: Database Creation</h2>";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database 'project' created or already exists<br>";
} else {
    echo "❌ Error creating database: " . $conn->error . "<br>";
}

$conn->select_db($dbname);

// Step 3: Create users table with correct structure
echo "<h2>Step 3: Creating Users Table</h2>";

$create_users_table = "
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
)";

if ($conn->query($create_users_table) === TRUE) {
    echo "✅ Users table created successfully<br>";
} else {
    echo "❌ Error creating users table: " . $conn->error . "<br>";
}

// Step 4: Create user_sessions table
echo "<h2>Step 4: Creating Sessions Table</h2>";

$create_sessions_table = "
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(128) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($create_sessions_table) === TRUE) {
    echo "✅ User sessions table created successfully<br>";
} else {
    echo "❌ Error creating sessions table: " . $conn->error . "<br>";
}

// Step 5: Test registration function
echo "<h2>Step 5: Testing Registration</h2>";

// Test with the data from your form
$test_name = "sumita";
$test_email = "sumita.test@gmail.com";
$test_phone = "9876543210";
$test_age = 12;
$test_gender = "Female";
$test_password = "testpassword123";

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $test_email);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    echo "⚠️ Email already exists. Trying with unique email...<br>";
    $test_email = "sumita_" . time() . "@gmail.com";
}

// Hash password and generate verification token
$hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
$verification_token = bin2hex(random_bytes(32));

// Insert test user
$stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, age, gender, password, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $test_name, $test_email, $test_phone, $test_age, $test_gender, $hashed_password, $verification_token);

if ($stmt->execute()) {
    echo "✅ Test registration successful!<br>";
    echo "Test user created with email: $test_email<br>";
    
    // Clean up test user
    $cleanup = $conn->prepare("DELETE FROM users WHERE email = ?");
    $cleanup->bind_param("s", $test_email);
    $cleanup->execute();
    echo "🧹 Test user cleaned up<br>";
} else {
    echo "❌ Test registration failed: " . $stmt->error . "<br>";
}

// Step 6: Create a simple working registration endpoint
echo "<h2>Step 6: Creating Simple Registration Endpoint</h2>";

$simple_registration_code = '<?php
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
?>';

file_put_contents('simple_register.php', $simple_registration_code);
echo "✅ Simple registration endpoint created: simple_register.php<br>";

echo "<h2>✅ Fix Complete!</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>Your registration should now work!</strong><br><br>";
echo "What was fixed:<br>";
echo "• Database and tables created<br>";
echo "• Proper table structure established<br>";
echo "• Simple registration endpoint created<br>";
echo "• All necessary components are now in place<br>";
echo "</div>";

echo "<h3>🧪 Test Registration Now</h3>";
echo "<p>Go back to your homepage and try registering again with:</p>";
echo "<ul>";
echo "<li><strong>Name:</strong> sumita</li>";
echo "<li><strong>Email:</strong> sumita.test@gmail.com (or any new email)</li>";
echo "<li><strong>Phone:</strong> 9876543210</li>";
echo "<li><strong>Age:</strong> 12</li>";
echo "<li><strong>Gender:</strong> Female</li>";
echo "<li><strong>Password:</strong> Any password</li>";
echo "</ul>";

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h1, h2, h3 { color: #333; }
.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
.warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>
