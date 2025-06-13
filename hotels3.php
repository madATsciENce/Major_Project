<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$a = $_GET["Area"];
if (isset($_POST['Area'])) {
    $b = "select * from table2 where Area ='$a'";
    $sql2 = mysqli_query($conn, $b);
    $bb = mysqli_fetch_assoc($sql2);
    $sql3 = mysqli_num_rows($sql2);

    if ($sql3 > 0) {
?>
        <table width="600" border="1" align="center">
            <tr>
                <td width="300">Area</td>
                <td width="300">Images</td>
            </tr>
            <?php
            $p = $_GET['Area'];
            $b = "select * from hotels where Area ='$p'";
            $dd = mysqli_query($conn, $b);
            $gg = mysqli_num_rows($dd);
            if ($gg > 0) {
                while ($ee = mysqli_fetch_assoc($dd)) {
            ?>
                    <tr>
                        <td width="200">
                            <?php echo $ee["Area"]; ?></td>
                        <td width="200"><img src="<?php echo $ee["Images"]; ?>" width="200" height="100"></td>
                    </tr>
    <?php
                }
            }
        }
    }
    ?>
        </table>