<html>
<body>
<?php 
$a= $_GET['Name']; 
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
echo $aa="Select * from registration_signup where name= '$a' ";
$c=mysqli_query($conn,$aa);
$dd = mysqli_fetch_assoc($c);
$bb= mysqli_num_rows($c);
if($bb>0)
{
?>
<form name="f" action="update.php" method="post">
          <tr>
Name:<td><input type="text" name="Name" size="10" value="<?php echo "$a"; ?>"></td><br><br>
Email:<td><input type="text" name="Email" size="30" value="<?php echo $dd["Email"]; ?>"></td><br><br>
Phone number:<td><input type="text" name="Phone_Number" size="10" value="<?php echo $dd["Phone_Number"]; ?>"></td><br><br>
Age:<td><input type="text" name="Age" size="10" value="<?php echo $dd["Age"]; ?>"></td><br><br>
Gender:<td><input type="text" name="Select_Gender" size="10" value="<?php echo $dd["Select_Gender"]; ?>"></td><br><br>
Password:<td><input type="text" name="Password" size="10" value="<?php echo $dd["Password"]; ?>"></td><br><br>
Confirm_Password:<td><input type="text" name="Confirm_Password" size="10" value="<?php echo $dd["Confirm_Password"]; ?>"></td><br><br>
<td><input type="submit" Name="Submit" value="Submit"></td>
</tr>
</form>
<?php
if(isset($_GET['Submit']))
{
?>
<input type="hidden" name="Name" value="<?php echo "$a"; ?>">
<?php
$a= $_GET['Name'];
$b= $_GET['Email'];
$c= $_GET['Phone_Number'];
$aa="Update registration_signup set Email= '$b', Phone_Number= '$c' where Name= '$a'";
$bb= mysqli_query($conn,$aa);
}
}
?>
</body>
</html>