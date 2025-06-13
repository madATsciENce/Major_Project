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

    $area = $_POST["Area"];
    $destination = $_POST["Destination"];
    $details = $_POST["hotels_details"];
    $amen1 = $_POST["Amenities"];
    $amen2 = $_POST["Amenities_one"];
    $amen3 = $_POST["Amenities_two"];
    $amen4 = $_POST["Amenities_three"];

    $check = "SELECT * FROM hotels WHERE Images = '$dest$nm'";
    $result = mysqli_query($conn, $check);
    if (mysqli_num_rows($result) == 0) {
        $insert = "INSERT INTO hotels VALUES('$area','$dest$nm','$details','$destination','$amen1','$amen2','$amen3','$amen4')";
        mysqli_query($conn, $insert);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hotel Listing</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #003580;
            margin-bottom: 30px;
        }

        .form-container {
            max-width: 1100px;
            margin: auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .form-container input[type="text"],
        .form-container input[type="file"] {
            width: 95%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-container input[type="submit"] {
            background-color: #0071c2;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .hotel-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-content {
            padding: 15px;
        }

        .card-content h3 {
            margin: 0;
            color: #003580;
            font-size: 20px;
        }

        .card-content p {
            margin: 5px 0;
            color: #444;
            font-size: 14px;
        }

        .amenities {
            color: #0071c2;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <h1>Hotel Uploader & Listings</h1>

    <div class="form-container">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-grid">
                <div><label>Area:<br><input type="text" name="Area" required></label></div>
                <div><label>Destination:<br><input type="text" name="Destination" required></label></div>
                <div><label>Upload Image:<br><input type="file" name="up" required></label></div>
                <div><label>Hotel Details:<br><input type="text" name="hotels_details" required></label></div>
                <div><label>Amenities 1:<br><input type="text" name="Amenities" required></label></div>
                <div><label>Amenities 2:<br><input type="text" name="Amenities_one"></label></div>
                <div><label>Amenities 3:<br><input type="text" name="Amenities_two"></label></div>
                <div><label>Amenities 4:<br><input type="text" name="Amenities_three"></label></div>
            </div>
            <center><input type="submit" name="submit" value="Submit"></center>
        </form>
    </div>

    <?php
    $result2 = mysqli_query($conn, "SELECT * FROM hotels");
    if (mysqli_num_rows($result2) > 0): ?>
        <div class="hotel-cards">
            <?php while ($dd = mysqli_fetch_assoc($result2)): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($dd["Images"]); ?>" alt="Hotel Image">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($dd["Destination"]); ?></h3>
                        <p><strong>Area:</strong> <?php echo htmlspecialchars($dd["Area"]); ?></p>
                        <p><?php echo htmlspecialchars($dd["hotels_details"]); ?></p>
                        <p class="amenities">
                            <?php echo htmlspecialchars($dd["Amenities"]) . ', ' . htmlspecialchars($dd["Amenities_one"]) . ', ' .
                                htmlspecialchars($dd["Amenities_two"]) . ', ' . htmlspecialchars($dd["Amenities_three"]); ?>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center;">No hotels listed yet.</p>
    <?php endif; ?>

</body>

</html>