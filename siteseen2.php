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
                <select name="pp">
                    <?php
                    $sql = "select distinct(Destination) from hotels where Destination != '' ";
                    $result = mysqli_query($conn, $sql);
                    $sql3 = mysqli_num_rows($result);

                    if ($sql3 > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <option value="<?php echo $row["Destination"]; ?>"><?php echo $row["Destination"]; ?> </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <center><input type="submit" name="Search" value="Search"></center>
            </td>
        </tr>
    </table>
</form>
<?php
if (isset($_GET['Search'])) {
?>
    <table width="100%" border="1" align="center">
        <tr>
            <td width="7%">Area</td>
            <td width="7%">Destination</td>
            <td width="10%">Images</td>
            <td width="10%">Hotel details</td>
            <td width="20%">Amenities</td>
            <td width="46%">Transport</td>
        </tr>
        <?php
        $a = $_GET['pp'];
        $b = "select * from hotels where Destination ='$a' ";
        $dd = mysqli_query($conn, $b);
        $gg = mysqli_num_rows($dd);
        if ($gg > 0) {
            while ($ee = mysqli_fetch_assoc($dd)) {
        ?>
                <tr>
                    <td width="7%">
                        <?php echo $ee["Area"]; ?></td>
                    <td width="7%"><?php echo $ee["Destination"]; ?></td>
                    <td width="10%"><img src="<?php echo $ee["Images"]; ?>" width="200" height="100"></td>
                    <td width="10%">
                        <?php echo $ee["hotels_details"]; ?><br><br><?php echo $ee["hotels_details_one"]; ?><br><br><?php echo $ee["hotels_details_two"]; ?><br><br><?php echo $ee["hotels_details_three"]; ?>
                    </td>
                    <td width="20%">
                        <?php echo $ee["Amenities"]; ?><br><br><?php echo $ee["Amenities_one"]; ?><br><br><?php echo $ee["Amenities_two"]; ?><br><br><?php echo $ee["Amenities_three"]; ?>
                    <td width="46%">
                        <?php if ($ee["Taxi"] != "") {
                        ?>
                            <?php echo $ee["Taxi"]; ?><br>
                        <?php
                        }
                        ?>
                        <?php if ($ee["Shared_Cabs"] != "") {
                        ?>
                            <?php echo $ee["Shared_Cabs"]; ?><br>
                        <?php
                        }
                        ?>
                        <?php if ($ee["Airport"] != "") {
                        ?>
                            <?php echo $ee["Airport"]; ?><br>
                        <?php
                        }
                        ?>
                        <?php if ($ee["Bus"] != "") {
                        ?>

                            <?php echo $ee["Bus"]; ?><br>
                        <?php
                        }
                        ?>

                        <?php if ($ee["Bicycle"] != "") {
                        ?>

                            <?php echo $ee["Bicycle"]; ?><br>
                        <?php
                        }
                        ?>

                        <?php if ($ee["Jeep"] != "") {
                        ?>

                            <?php echo $ee["Jeep"]; ?><br>
                        <?php
                        }
                        ?>

                        <?php if ($ee["Train"] != "") {
                        ?>

                            <?php echo $ee["Train"]; ?>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
    <?php
            }
        }
    }
    ?>
    </table>
    </body>

    </html>