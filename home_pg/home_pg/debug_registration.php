<?php
// Debug Registration Issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Registration Debug Tool</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error . "<br>";
    die();
} else {
    echo "✅ Database connection successful<br>";
}

// Check if users table exists
echo "<h3>2. Table Structure Check</h3>";
$tables_to_check = ['users', 'user_sessions', 'destinations', 'hotels', 'packages', 'bookings', 'payments'];

foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✅ Table '$table' exists<br>";
        
        // Show table structure for users table
        if ($table === 'users') {
            echo "<details><summary>Users table structure:</summary>";
            $structure = $conn->query("DESCRIBE users");
            echo "<table border='1' style='margin: 10px;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $structure->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table></details>";
        }
    } else {
        echo "❌ Table '$table' does not exist<br>";
    }
}

// Test registration with sample data
echo "<h3>3. Registration Test</h3>";

// Simulate the registration data from your form
$test_data = [
    'name' => 'Sumita Baidya',
    'email' => 'canikissya528@gmail.com',
    'phone' => '1234567890',
    'age' => '17',
    'gender' => 'Female',
    'password' => 'testpassword123'
];

echo "<strong>Test Data:</strong><br>";
foreach ($test_data as $key => $value) {
    echo "$key: $value<br>";
}

// Check if email already exists
echo "<br><strong>Email Check:</strong><br>";
$stmt = $conn->prepare("SELECT id, email, verified FROM users WHERE email = ?");
$stmt->bind_param("s", $test_data['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existing_user = $result->fetch_assoc();
    echo "⚠️ Email already exists in database<br>";
    echo "User ID: {$existing_user['id']}<br>";
    echo "Verified: " . ($existing_user['verified'] ? 'Yes' : 'No') . "<br>";
    echo "<br><strong>Solution:</strong> Try with a different email address or delete the existing user.<br>";
} else {
    echo "✅ Email is available<br>";
}

// Test the actual registration function
echo "<h3>4. Registration Function Test</h3>";

try {
    require_once 'auth_handler.php';
    $auth = new AuthHandler();
    
    // Use a unique email for testing
    $unique_email = 'test_' . time() . '@example.com';
    $result = $auth->register(
        $test_data['name'],
        $unique_email,
        $test_data['phone'],
        $test_data['age'],
        $test_data['gender'],
        $test_data['password']
    );
    
    echo "<strong>Registration Result:</strong><br>";
    echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "<br>";
    echo "Message: " . $result['message'] . "<br>";
    
    if ($result['success']) {
        echo "✅ Registration function works correctly<br>";
        
        // Clean up test user
        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $unique_email);
        $stmt->execute();
        echo "🧹 Test user cleaned up<br>";
    } else {
        echo "❌ Registration function failed<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing registration: " . $e->getMessage() . "<br>";
}

// Check PHP configuration
echo "<h3>5. PHP Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error Reporting: " . (error_reporting() ? 'Enabled' : 'Disabled') . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "<br>";

// Check if required PHP extensions are loaded
$required_extensions = ['mysqli', 'json', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext extension loaded<br>";
    } else {
        echo "❌ $ext extension not loaded<br>";
    }
}

// Test AJAX endpoint
echo "<h3>6. AJAX Endpoint Test</h3>";
echo "<button onclick='testRegistration()'>Test Registration AJAX</button>";
echo "<div id='ajax-result'></div>";

$conn->close();
?>

<script>
async function testRegistration() {
    const resultDiv = document.getElementById('ajax-result');
    resultDiv.innerHTML = 'Testing...';
    
    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', 'Test User');
    formData.append('email', 'test_' + Date.now() + '@example.com');
    formData.append('phone', '9876543210');
    formData.append('age', '25');
    formData.append('gender', 'Male');
    formData.append('password', 'testpass123');
    
    try {
        const response = await fetch('auth_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        resultDiv.innerHTML = `
            <strong>AJAX Test Result:</strong><br>
            Success: ${data.success ? 'Yes' : 'No'}<br>
            Message: ${data.message}<br>
            <br>
            ${data.success ? '✅ AJAX registration works!' : '❌ AJAX registration failed!'}
        `;
    } catch (error) {
        resultDiv.innerHTML = `❌ AJAX Error: ${error.message}`;
    }
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
table { border-collapse: collapse; }
th, td { padding: 8px; text-align: left; }
button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #005a87; }
details { margin: 10px 0; }
summary { cursor: pointer; font-weight: bold; }
</style>

<hr>
<h3>🛠️ Common Solutions:</h3>
<ol>
    <li><strong>Email already exists:</strong> Use a different email or delete the existing user from the database</li>
    <li><strong>Database table missing:</strong> Run the setup_database.php script</li>
    <li><strong>Database connection error:</strong> Check if MySQL is running and credentials are correct</li>
    <li><strong>PHP errors:</strong> Check the error log or enable error display</li>
    <li><strong>AJAX issues:</strong> Check browser console for JavaScript errors</li>
</ol>

<p><strong>Next Steps:</strong></p>
<ul>
    <li>If email exists, try with a different email address</li>
    <li>If tables are missing, run <a href="setup_database.php">setup_database.php</a></li>
    <li>Check browser console (F12) for JavaScript errors</li>
    <li>Verify all files are in the correct directory</li>
</ul>
