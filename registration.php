<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Untitled Document</title>
    <style>
    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }
    </style>
</head>

<body>
    <?php 
$a= $_POST['Name']; 
$b= $_POST['Email']; 
$c= $_POST['Phone_Number']; 
$d= $_POST['Age']; 
$e= $_POST['Select_Gender']; 
$f= $_POST['Password']; 
$g= $_POST['Confirm_Password'];  

// Validation functions
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@gmail\.com$/', $email);
}

function isValidPhoneNumber($phone) {
    // Check if phone contains only digits and exactly 10 digits
    return preg_match('/^[0-9]{10}$/', $phone);
}

function isValidAge($age) {
    return is_numeric($age) && $age > 0;
}

function isValidPassword($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);
    return $uppercase && $lowercase && $number && $specialChars && strlen($password) >= 8;
}

if (!isValidEmail($b)) {
    echo "<script>document.getElementById('emailError').innerText = 'Write correct gmail id ending with @gmail.com';</script>";
    exit();
}

if (!isValidPhoneNumber($c)) {
    echo "<script>document.getElementById('phoneError').innerText = 'Input correct phone number with exactly 10 digits';</script>";
    exit();
}

if (!isValidAge($d)) {
    echo "<script>alert('Input valid age (numbers only)'); window.location.href='sign.php';</script>";
    exit();
}

if (!isValidPassword($f)) {
    echo "<script>alert('Password must include uppercase, lowercase, number, special character and be at least 8 characters long'); window.location.href='sign.php';</script>";
    exit();
}

if ($f !== $g) {
    echo "<script>alert('Passwords do not match'); window.location.href='sign.php';</script>";
    exit();
}

?>

    <script>
    function validateForm() {
        var email = document.forms["signupForm"]["Email"].value;
        var phone = document.forms["signupForm"]["Phone_Number"].value;
        var emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        var phonePattern = /^\d{10}$/;

        var emailError = document.getElementById("emailError");
        var phoneError = document.getElementById("phoneError");
        emailError.innerHTML = "";
        phoneError.innerHTML = "";

        var valid = true;

        if (!emailPattern.test(email)) {
            emailError.innerHTML = "Write correct gmail id ending with @gmail.com";
            valid = false;
        }
        if (!phonePattern.test(phone)) {
            phoneError.innerHTML = "Input correct phone number with exactly 10 digits";
            valid = false;
        }
        return valid;
    }
    </script>

    <form name="signupForm" method="post" onsubmit="return validateForm()">
        <label for="Email">Email:</label>
        <input type="text" name="Email" id="Email" placeholder="Enter your email" />
        <div id="emailError" class="error-message"></div>

        <label for="Phone_Number">Phone Number:</label>
        <input type="text" name="Phone_Number" id="Phone_Number" placeholder="Enter 10 digit phone number" />
        <div id="phoneError" class="error-message"></div>

        <label for="Name">Name:</label>
        <input type="text" name="Name" id="Name" placeholder="Enter your name" />

        <label for="Age">Age:</label>
        <input type="number" name="Age" id="Age" placeholder="Enter your age" />

        <label for="Select_Gender">Gender:</label>
        <select name="Select_Gender" id="Select_Gender">
            <option value="Female">Female</option>
            <option value="Male">Male</option>
            <option value="Other">Other</option>
        </select>

        <label for="Password">Password:</label>
        <input type="password" name="Password" id="Password" placeholder="Enter password" />

        <label for="Confirm_Password">Confirm Password:</label>
        <input type="password" name="Confirm_Password" id="Confirm_Password" placeholder="Confirm password" />

        <input type="submit" value="Sign Up" />
    </form>

    <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$check_email = "SELECT * FROM registration_signup WHERE Email='$b'";
$result_check = mysqli_query($conn, $check_email);

if (mysqli_num_rows($result_check) > 0) {
    echo "<script>alert('Email already exists. Please use a different email or sign in.'); window.location.href='sign.php';</script>";
    exit();
} else {
    $aa = "INSERT INTO registration_signup VALUES('$a','$b','$c','$d','$e','$f','$g')";
    $bb = mysqli_query($conn, $aa);
    if ($bb) {
        echo "<script>alert('Registration successful. You can now sign in.'); window.location.href='signin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Registration failed. Please try again.'); window.location.href='sign.php';</script>";
        exit();
    }
}

?>

</body>

</html>