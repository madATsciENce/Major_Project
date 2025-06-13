<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hotel Search by Area</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin: 20px auto;
            width: 420px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        select,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 16px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px #ccc;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        img {
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <h2>Search Hotels by Area</h2>

    <form name="f" method="GET" action="">
        <label for="pp">Select Area:</label>
        <select name="pp" id="pp" required>
            <option value="">-- Select Area --</option>
            <?php
            $sql = "SELECT DISTINCT Area FROM hotels";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . htmlspecialchars($row["Area"]) . '">' . htmlspecialchars($row["Area"]) . '</option>';
                }
            }
            ?>
        </select>
        <input type="submit" name="Search" value="Search">
    </form>

    <?php
    if (isset($_GET['Search']) && !empty($_GET['pp'])) {
        $area = $conn->real_escape_string($_GET['pp']);
        $query = "SELECT * FROM hotels WHERE Area = '$area'";
        $results = mysqli_query($conn, $query);

        if ($results && mysqli_num_rows($results) > 0) {
            echo '<table>';
            echo '<tr><th>Area</th><th>Image</th></tr>';
            while ($hotel = mysqli_fetch_assoc($results)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($hotel["Area"]) . '</td>';
                echo '<td><img src="' . htmlspecialchars($hotel["Images"]) . '" width="200" height="100" alt="Hotel Image"></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p style='text-align: center;'>No hotels found in selected area.</p>";
        }
    }
    ?>

</body>

</html>