<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>

</head>

<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['Reset_Password']))
{
$b= $_POST['Email']; 
?> 
<?php
$aa="Select * from registration_signup where Email= '$b'";
if($bb= mysqli_query($conn,$aa))
{
$c = mysqli_num_rows($bb);
if($c>0)
{
echo "the email is exist";?>
<form id="f" action="forget1.php" method="get">
<input type="hidden" name="Email" id="Email" value="<?php echo $b; ?>">
Password: <input type="password" id="Password" name="Password"><br><br>
Confirm Password: <input type="password" id="Confirm_Password" name="Confirm_Password"><br><br>
<span id="passwordError" ></span>

<input type="submit" name="Submit" id="Submit" value="Submit">

<?php
} 
else
{
echo "The email is not found";
}
}

}
?>

<?php
if(isset($_GET['Submit']))
{
?>
<input type="hidden" name="Email" value="<?php echo $_GET['Email']; ?>">
<?php
$f = $_GET['Password'];
$g = $_GET['Confirm_Password'];
$b= $_GET['Email'];
$aa="Update registration_signup set Password= '$f',Confirm_Password= '$g' where Email= '$b'";
$bb= mysqli_query($conn,$aa);
} 

?>
<script>
        document.getElementById('f').addEventListener('input', function () {
            validateForm();
        });
	 function validateForm() {
	const email = document.getElementById('Email').value;
	const password = document.getElementById('Password').value;
            const confirmPassword = document.getElementById('Confirm_Password').value;
		const errorElement = document.getElementById('passwordError');
	const submitButton = document.getElementById('Submit');
   
	let isValid = true;

            if (!email || !password || !confirmPassword) {
                isValid = false;
            }
if (password.length < 8) {
        errorElement.textContent  = 'Password must be at least 8 characters long.';
	errorElement.classList.remove('success');
                errorElement.classList.add('error');
		isValid = false;

    } else if (!/[a-z]/.test(password)) {
        errorElement.textContent = 'Password must contain at least one lowercase letter (a-z).';
	errorElement.classList.remove('success');
                errorElement.classList.add('error');
		isValid = false;

    } else if (!/[A-Z]/.test(password)) {
        errorElement.textContent = 'Password must contain at least one uppercase letter (A-Z).';
	errorElement.classList.remove('success');
                errorElement.classList.add('error');
		isValid = false;

    } else if (!/\d/.test(password)) {
        errorElement.textContent = 'Password must contain at least one number (0-9).';
	errorElement.classList.remove('success');
                errorElement.classList.add('error');
		isValid = false;

    } else if (!/[\W_]/.test(password)) {
        errorElement.textContent = 'Password must contain at least one special character (!@#$%^&* etc.).';
	errorElement.classList.remove('success');
                errorElement.classList.add('error');
		isValid = false;

    } else if (password !== confirmPassword) {
        errorElement.textContent= 'Passwords do not match.';
                errorElement.classList.remove('success');
                errorElement.classList.add('error');
                isValid = false;
            } else {
                errorElement.textContent = 'Passwords match';
                errorElement.classList.remove('error');
                errorElement.classList.add('success');
            }
	
            if (isValid) {
                submitButton.classList.add('enabled');
                submitButton.disabled = false;
            } else {
                submitButton.classList.remove('enabled');
                submitButton.disabled = true;
            }
	if (errorMessage) {
                submitButton.classList.add('enabled');
                submitButton.disabled = false;
            } else {
                submitButton.classList.remove('enabled');
                submitButton.disabled = true;
            }

        }
    </script>

</body>
</html>