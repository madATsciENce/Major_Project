<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>

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
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['Submit']))
{
echo $aa="Update registration_signup set Email='$b',Phone_Number='$c' where Name='$a'";
$bb= mysqli_query($conn,$aa);
} 
?>
</body>
</html>
