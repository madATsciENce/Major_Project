<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $dest = "Himages/";
    $nm = $_FILES['up']['name'];
    $source = $_FILES['up']['tmp_name'];
    move_uploaded_file($source, $dest . $nm);
    $b = $_POST["Destination"];
    $d = $_POST["Details"];
    $sql = "select * from Siteseen where Images = '$dest$nm' ";
    $sql2 = mysqli_query($conn, $sql);
    $sql3 = mysqli_num_rows($sql2);
    if ($sql3 == 0) {
        $query = "insert into siteseen values('$b','$dest$nm','$d')";
        $query2 = mysqli_query($conn, $query);
    }
}
?>
<form name="frm" method="post" action="Siteseen.php" enctype="multipart/form-data">
    <table width="880" border="1" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>Destination:<input type="text" name="Destination"></td>
            <td class="text6"><input type="file" name="up" id="up" /></td>
            <td> Details:<input type="text" name="Details"></td>
        </tr>
        <td class="text6" align="center"><input type="submit" name="submit" value="Submit" /></td>
        </tr>
    </table>

    </table>
</form>
<br />
<?php
$p = "select * from siteseen";
$result2 = mysqli_query($conn, $p);
echo $c = mysqli_num_rows($result2);
?>
<table width="80%" border="1">
    <tr>
        <td width="20%">
            <h2>
                <center>Destination</center>
            </h2>
        </td>
        <td width="20%">
            <h2>
                <center>Images</center>
            </h2>
        </td>
        <td width="20%">
            <h2>
                <center>Details</center>
            </h2>
        </td>
    </tr>
    <?php
    while ($dd = mysqli_fetch_assoc($result2)) {
    ?>
        <table width="80%" border="1">
            <tr>
                <td colspan="1" width="20%"><?php echo $dd["Destination"]; ?></td>
                <td colspan="1" width="20%"><img src="<?php echo $dd["Images"]; ?>" width="190" height="150" border="2">
                </td>
                <td colspan="1" width="20%"><?php echo $dd["Details"]; ?></td>
            <tr>

            </tr>
        </table>
    <?php
    }
    ?>