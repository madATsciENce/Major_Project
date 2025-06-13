<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit']))
{
$dest="images/";
$nm = $_FILES['up']['name'];
$source= $_FILES['up']['tmp_name'];
move_uploaded_file($source,$dest.$nm);
echo $a=$_POST["Direction"];
echo $b=$_POST["Area"];
$sql = "select * from table2 where images = '$dest$nm' ";
$sql2 = mysqli_query($conn,$sql);
echo $sql3 = mysqli_num_rows($sql2);

if($sql3 == 0)
{
echo $query = "insert into table2 values('$a','$b','$dest$nm')";
$query2 = mysqli_query($conn,$query);
}
}
?>


<body>
<form name="frm" method="post" action="" enctype="multipart/form-data">
<table width="880" border="1" align="center" cellpadding="0" cellspacing="0">
<tr>
<td>Direction: <input type="text" name="Direction"></td>
<td>Area:<input type="text" name="Area"></td>
<td class="text6"><input type="file" name="up" id="up" /></td>
<td class="text6" align="center"><input type="submit" name="submit" value="Submit"  /></td></tr>
</table>
</form>
<br />
<?php
$p="select * from table2" ;
$result2 = mysqli_query($conn,$p);
$c = mysqli_num_rows($result2);
?>
<table width="80%" border="1">
<tr>
<td width="20%"><h2><center>Direction</center></h2></td><td width="20%"><h2><center>Area</center></h2></td><td width="20%"><h2><center>Images</center></h2></td>
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
<td colspan="1" width="20%"><img src="<?php echo $dd["Images"]; ?>" width="190" height="150" border="2"></td>
</tr>
</table>
<?php
}
?>

</body>
</html>