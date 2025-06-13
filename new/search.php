<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<form method="POST" action="">
    <label for="search">Search by Area:</label>
    <input type="text" name="search" id="search" required>
    <input type="submit" value="Search">
</form>

<?php
if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT * FROM hotels WHERE Area ='$search'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>
                <th>Area</th>
                <th>Images</th>
                <th>Destination</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            $area = htmlspecialchars($row['Area']);
            $image = htmlspecialchars($row['Images']);
            $destination = htmlspecialchars($row['Destination']);

            echo "<tr>
                    <td>$area</td>
                    <td><img src='$image' width='190' height='150'></td>
                    <td>$destination</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }

    $conn->close();
}
?>



















