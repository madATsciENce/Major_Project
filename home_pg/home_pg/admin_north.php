<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['admin_signin']))
{
    $email = $_POST['Email'];
    $pass = $_POST['Password'];

    $sql = "SELECT * FROM admin_users WHERE Email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        // Verify password hash
        if(password_verify($pass, $row['Password']))
        {
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_role'] = $row['role']; // store role in session
            header("Location: admin_dashboard.php");
        }
        else
        {
            echo "Invalid password";
        }
    }
    else
    {
        echo "Invalid email";
    }
}
?>