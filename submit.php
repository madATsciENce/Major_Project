<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<form name="f" method="Get" action="">
    <table width="400" border="1" align="center">
        <tr>
            <td>
                <select name="Area">
                    <?php
                    $sql = "select distinct(Area) from hotels";
                    $result = mysqli_query($conn, $sql);
                    $sql3 = mysqli_num_rows($result);

                    if ($sql3 > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <option value="<?php echo $row["Area"]; ?>"><?php echo $row["Area"]; ?> </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <center><input type="submit" name="Submit" value="Submit"></center>
            </td>
        </tr>
    </table>
</form>
<?php
if (isset($_GET['Submit'])) {
    $a = $_GET['Area'];
    if ($a == "Rishikesh" || $a == "Wayanad") {
        echo "<center><h3>Valid Area: $a</h3></center>";
        echo $aa = "insert into destination values('$a')";
        $bb = mysqli_query($conn, $aa);
    } else {
        echo "<center><h3>Only Rishikesh and Wayanad are allowed.</h3></center>";
    }
}
?>