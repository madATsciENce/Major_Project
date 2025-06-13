<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sightseeing by Destination</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #0071c2;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .message {
            text-align: center;
            font-size: 18px;
            color: red;
            margin: 20px;
        }
    </style>
</head>

<body>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "6thsemproject";

    // Connect
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<div class='message'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Validate input
    if (isset($_GET['Destination'])) {
        $destination = $conn->real_escape_string($_GET['Destination']);

        $query = "SELECT * FROM siteseen WHERE Destination = '$destination'";
        $result = mysqli_query($conn, $query);
        $count = mysqli_num_rows($result);

        if ($count > 0) {
            echo "<h2>Sightseeing in " . htmlspecialchars($destination) . "</h2>";
            echo '<table>
                <tr>
                    <th>Destination</th>
                    <th>Image</th>
                    <th>Details</th>
                </tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                    <td>' . htmlspecialchars($row["Destination"]) . '</td>
                    <td><img src="' . htmlspecialchars($row["Images"]) . '" width="200" height="160"></td>
                    <td>' . htmlspecialchars($row["Details"]) . '</td>
                  </tr>';
            }
            echo '</table>';
        } else {
            echo "<div class='message'>No sightseeing places found for '$destination'.</div>";
            include("hotels4.php");
        }
    } else {
        echo "<div class='message'>Destination not provided in the URL.</div>";
    }
    ?>

</body>

</html>