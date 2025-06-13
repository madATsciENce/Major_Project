<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql2="select * from table1" ;
$result2 = mysqli_query($conn,$sql2);
$c = mysqli_num_rows($result2);
?>
<table width="80%" border="1">
<tr>
<td width="20%"><h2><center>Direction</center></h2></td><td width="20%"><h2><center>Area</center></h2></td><td width="20%"><h2><center>Images</center></h2></td><td width="20%"><h2><center>Hyperlink</center></h2></td>
</tr>
<table>
<?php
while($dd= mysqli_fetch_assoc($result2))
{
?>
<table width="80%" border="1">
<tr>
<td width="20%"><?php echo $dd["Direction"]; ?></td>
<td colspan="1" width="20%"><?php echo $dd["Area"]; ?></td>
<td colspan="1" width="20%"><img src="<?php echo $dd["images"]; ?>" width="190" height="150" border="2"></td>
<td colspan="1" width="20%"><a href="<?php echo $dd["Hyperlink"]; ?>">Click on</a></td>
</tr>
</table>
<?php
}
?>


