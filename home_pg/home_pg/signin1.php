<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php 
$b= $_POST['Email'];   
$f= $_POST['Password']; 
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

$aa="Select * from registration_signup where Email= '$b' and Password= '$f' ";
$bb= mysqli_query($conn,$aa);
$cc = mysqli_fetch_assoc($bb);
$c = mysqli_num_rows($bb);
if($c>0)
{
echo "The email is exist";
?>

<input type="text" name="Name" value="<?php echo $cc["Name"] ?>">
<?php
include("north.php");
}
else
{
echo "The email is not exist";
include("signin.php");
}

?>

</body>
</html>