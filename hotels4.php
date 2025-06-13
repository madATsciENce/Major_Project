<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Find Hotels in Your Destination</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #0071c2;
            --background: #f4f6f9;
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--background);
        }

        header {
            background: #fff;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: var(--primary-color);
            font-size: 24px;
        }

        nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .hero {
            background: linear-gradient(to right, #0071c2, #3fa9f5);
            color: white;
            text-align: center;
            padding: 60px 20px;
        }

        .hero h2 {
            font-size: 36px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 40px 20px;
        }

        .hotel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
        }

        .card-body h3 {
            margin-bottom: 10px;
            color: var(--primary-color);
            font-size: 20px;
        }

        .card-body p {
            font-size: 14px;
            color: #444;
            margin: 5px 0;
        }

        .card-body a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .message {
            text-align: center;
            color: crimson;
            font-size: 18px;
            margin-top: 30px;
        }

        footer {
            background: #f1f1f1;
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>

    <header>
        <h1>Safar</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="destinations.php">Destinations</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <div class="hero">
        <h2>Find the Best Hotels for Your Trip</h2>
    </div>

    <div class="container">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "6thsemproject";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("<div class='message'>Connection failed: " . $conn->connect_error . "</div>");
        }

        if (isset($_GET['area'])) {
            $area = $conn->real_escape_string($_GET['area']);
            $query = "SELECT * FROM hotels WHERE Area = '$area'";
            $result = mysqli_query($conn, $query);
            $count = mysqli_num_rows($result);

            if ($count > 0) {
                echo "<h2 style='text-align:center; margin-bottom: 30px;'>Hotels in " . htmlspecialchars($area) . "</h2>";
                echo '<div class="hotel-grid">';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '
            <div class="card">
                <img src="' . htmlspecialchars($row["Images"]) . '" alt="Hotel">
                <div class="card-body">
                    <h3>' . htmlspecialchars($row["Destination"]) . '</h3>
                    <p><strong>Details:</strong> ' . htmlspecialchars($row["hotels_details"]) . '</p>
                    <p><strong>Amenities:</strong><br>' .
                        htmlspecialchars($row["Amenities"]) . '<br>' .
                        htmlspecialchars($row["Amenities_one"]) . '<br>' .
                        htmlspecialchars($row["Amenities_two"]) . '<br>' .
                        htmlspecialchars($row["Amenities_three"]) .
                        '</p>
                    <a href="siteseen1.php?Destination=' . urlencode($row["Destination"]) . '">Explore</a>
                </div>
            </div>';
                }
                echo '</div>';
            } else {
                echo "<div class='message'>No hotels found in this area.</div>";
                include("north.php");
            }
        } else {
            echo "<div class='message'>No area selected.</div>";
        }
        ?>
    </div>

    <footer>
        &copy; 2025 Kundu Travel. All rights reserved.
    </footer>

</body>

</html>