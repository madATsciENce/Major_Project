<?php 
$a= $_POST['Name']; 
$b= $_POST['Email']; 
$c= $_POST['Phone_Number']; 
$d= $_POST['Age']; 
$e= $_POST['Select_Gender']; 
$f= $_POST['Password']; 
$g= $_POST['Confirm_Password'];  

// Validate phone number length
if(strlen($c) != 10) {
    die("Phone number must be exactly 10 digits.");
}

// Validate email contains @gmail.com
if(strpos($b, '@gmail.com') === false) {
    die("Email must contain '@gmail.com'.");
}

// Hash the password before storing
$hashed_password = password_hash($f, PASSWORD_DEFAULT);

// Generate email verification token
$verification_token = bin2hex(random_bytes(16));
$verified = 0; // 0 means not verified, 1 means verified

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Insert user with verification token and verified status
$aa = "insert into registration_signup (Name, Email, Phone_Number, Age, Select_Gender, Password, Confirm_Password, verification_token, verified) values('$a','$b','$c','$d','$e','$hashed_password','$g','$verification_token','$verified')";
$bb = mysqli_query($conn,$aa);

// Send verification email
$verification_link = "http://yourdomain.com/project/home_pg/home_pg/verify_email.php?token=" . $verification_token;
$subject = "Email Verification";
$message = "Please click the following link to verify your email: " . $verification_link;
$headers = "From: no-reply@yourdomain.com";

mail($b, $subject, $message, $headers);

?>