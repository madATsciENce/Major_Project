<?php
// Debug version of auth handler
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

session_start();

// Test database connection first
echo "<h2>🔍 Debug Auth Handler</h2>";

try {
    $conn = new mysqli("localhost", "root", "", "project");
    if ($conn->connect_error) {
        echo "❌ Database connection failed: " . $conn->connect_error . "<br>";
        die();
    } else {
        echo "✅ Database connection successful<br>";
    }

    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✅ Users table exists<br>";
        
        // Show table structure
        $structure = $conn->query("DESCRIBE users");
        echo "<details><summary>Users table structure:</summary>";
        echo "<table border='1' style='margin: 10px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table></details>";
    } else {
        echo "❌ Users table does not exist<br>";
        echo "<strong>Solution:</strong> Run the database setup SQL in phpMyAdmin<br>";
    }

    // Test registration manually
    echo "<h3>Manual Registration Test</h3>";
    
    $test_name = "sumita";
    $test_email = "sumita_debug_" . time() . "@gmail.com";
    $test_phone = "9876543210";
    $test_age = 12;
    $test_gender = "Female";
    $test_password = "test123";
    
    echo "Testing with data:<br>";
    echo "Name: $test_name<br>";
    echo "Email: $test_email<br>";
    echo "Phone: $test_phone<br>";
    echo "Age: $test_age<br>";
    echo "Gender: $test_gender<br>";
    
    // Check if email validation works
    if (strlen($test_phone) != 10) {
        echo "❌ Phone validation failed<br>";
    } else {
        echo "✅ Phone validation passed<br>";
    }
    
    if (strpos($test_email, '@') === false) {
        echo "❌ Email validation failed<br>";
    } else {
        echo "✅ Email validation passed<br>";
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        echo "❌ Prepare statement failed: " . $conn->error . "<br>";
    } else {
        $stmt->bind_param("s", $test_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "⚠️ Email already exists<br>";
        } else {
            echo "✅ Email is available<br>";
            
            // Try to insert user
            $hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(32));
            
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, age, gender, password, verification_token, verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            if (!$insert_stmt) {
                echo "❌ Insert prepare failed: " . $conn->error . "<br>";
            } else {
                $insert_stmt->bind_param("sssssss", $test_name, $test_email, $test_phone, $test_age, $test_gender, $hashed_password, $verification_token);
                
                if ($insert_stmt->execute()) {
                    echo "✅ Manual registration successful!<br>";
                    
                    // Clean up test user
                    $cleanup = $conn->prepare("DELETE FROM users WHERE email = ?");
                    $cleanup->bind_param("s", $test_email);
                    $cleanup->execute();
                    echo "🧹 Test user cleaned up<br>";
                } else {
                    echo "❌ Insert failed: " . $insert_stmt->error . "<br>";
                }
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

// Now test the AJAX endpoint
echo "<h3>AJAX Endpoint Test</h3>";

// Clear any output buffer
ob_end_clean();

// Test if we can handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];
        echo json_encode(['debug' => 'POST request received', 'action' => $action]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Auth Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        details { margin: 10px 0; }
        .test-section { background: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }
    </style>
</head>
<body>

<div class="test-section">
    <h3>🧪 Test AJAX Registration</h3>
    <button onclick="testAjaxRegistration()">Test AJAX Call</button>
    <div id="ajax-result" style="margin-top: 10px;"></div>
</div>

<div class="test-section">
    <h3>📋 Next Steps</h3>
    <ol>
        <li>If database connection failed: Make sure MySQL is running</li>
        <li>If users table doesn't exist: Run the SQL setup in phpMyAdmin</li>
        <li>If manual registration failed: Check the error message above</li>
        <li>If AJAX test fails: Check browser console for errors</li>
    </ol>
</div>

<script>
async function testAjaxRegistration() {
    const resultDiv = document.getElementById('ajax-result');
    resultDiv.innerHTML = 'Testing AJAX...';
    
    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', 'Test User');
    formData.append('email', 'test_' + Date.now() + '@example.com');
    formData.append('phone', '9876543210');
    formData.append('age', '25');
    formData.append('gender', 'Male');
    formData.append('password', 'testpass123');
    
    try {
        const response = await fetch('debug_auth.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            resultDiv.innerHTML = `
                <strong>AJAX Success:</strong><br>
                ${JSON.stringify(data, null, 2)}
            `;
        } catch (parseError) {
            resultDiv.innerHTML = `
                <strong>JSON Parse Error:</strong><br>
                Response was not valid JSON:<br>
                <pre>${text}</pre>
            `;
        }
    } catch (error) {
        resultDiv.innerHTML = `<strong>AJAX Error:</strong> ${error.message}`;
    }
}
</script>

</body>
</html>
