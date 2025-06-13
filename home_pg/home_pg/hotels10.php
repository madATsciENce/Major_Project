<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo $name = $_GET["name"];
echo $aa="Select * from hotels where Area = '$name' ";
$bb= mysqli_query($conn,$aa);
$c = mysqli_num_rows($bb);
?>
<table width="80%" border="1">
<tr>
<td width="20%"><h2><center>Area</center></h2></td><td width="20%"><h2><center>Images</center></h2></td>
</tr>
<table>
<?php
while($dd= mysqli_fetch_assoc($bb))
{
?>
<table width="100%" border="1">
<tr>
<td colspan="1" width="10%"><?php echo $dd["Area"]; ?></td>
<td colspan="1" width="10%"><?php echo $dd["Destination"]; ?></td>
<td colspan="1" width="30%"><img src="<?php echo $dd["Images"]; ?>" width="190" height="150" border="2"></td>
<td colspan="1" width="10%"><?php echo $dd["hotels_details"]; ?></td>
<td colspan="1" width="10%"><?php echo $dd["Amenities"]; ?></td>
<td colspan="1" width="10%"><?php echo $dd["Amenities_one"]; ?></td>
<td colspan="1" width="10%"><?php echo $dd["Amenities_two"]; ?></td>
<td colspan="1" width="10%"><?php echo $dd["Amenities_three"]; ?></td>




</tr>
</table>
<?php
}
?>

