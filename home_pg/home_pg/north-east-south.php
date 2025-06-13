<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camping Travel Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("camping2.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            position: relative;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("camping2.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: blur(5px); /* Apply blur effect */
            z-index: -1; /* Place it behind the content */
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }
        .category {
            text-align: center;
            margin: 50px 0;
            padding: 50px 0;
            color: white;
        }
        .category h2 {
            margin-bottom: 30px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }
        .places-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 80px;
        }
        .place {
            position: relative;
            width: 250px;
            transition: transform 0.3s ease;
        }
        .place img {
            width: 120%;
	    height:75%;
            border-radius: 10px;
        }
        .place:hover {
            transform: scale(1.1);
        }
        .place a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        .place a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>


<?php
$sql2="select * from table1" ;
$result2 = mysqli_query($conn,$sql2);
echo $c = mysqli_num_rows($result2);
$d=$c/3;

?>
<center>
<table width="980" border="0">
<tr>
<td>
<center>
<table width="970" border="1">
<?php 
for($i=1;$i<=$d;$i++)
{
$p=1;
?>
<tr>
<?php
while($row2 = mysqli_fetch_assoc($result2))
{
if($p<=3)
{
?>
<td width="195" height="200"><center><a href="<?php echo $row2["Hyperlink"]; ?>"><img src="<?php echo $row2["images"]; ?>" width="190" height="150" border="20"></a>
<input type="text" size="5" value="<?php echo $row2["Area"]; ?>"></center></td>
<?php
$p++;
}
else
{
?>
<td width="195" height="200"><center><a href="<?php echo $row2["Hyperlink"]; ?>"><img src="<?php echo $row2["images"]; ?>" width="190" height="150" border="2"></a>
<input type="text" size="5" value="<?php echo $row2["Area"]; ?>"></center></td>
<?php
break;
}
}
?>
</tr>
<?php
}
?>

</table>
</center>
</td>
</tr>
</table>
</center>

    <h1>Explore Camping Places</h1>

    <!-- North Section -->
    <div class="category">
        <h2>North</h2>
        <div class="places-container">
            <div class="place">
                <a href="Rishikesh.html">
                    <img src="Rishikesh.jpeg">
                    <p>Rishikesh</p>
                </a>
            </div>
            <div class="place">
                <a href="chandra.html">
                    <img src="chandratal-lake-1.jpg">
                    <p>Chandratal Lake</p>
                </a>
            </div>
	<div class="place">
                <a href="mussoorie.html">
                    <img src="Best-of-Mussoorie-with-Rishikesh-and-Haridwar-1.webp">
                    <p>Mussoorie</p>
                </a>
            </div>
	<div class="place">
                <a href="spiti.html">
                    <img src="images/Spiti/stones-at-chandrataal-lake-1.jpg">
                    <p>Spiti</p>
                </a>
            </div>
	<div class="place">
                <a href="sarchu.html">
                    <img src="images/Sarchu/Sarchu.jpeg">
                    <p>Sarchu</p>
                </a>
            </div>
<div class="place">
                <a href="jaisalmar.html">
                    <img src="images/Jaisalmer/cover.jpg">
                    <p>Jaisalmar</p>
                </a>
            </div>





        </div>
    </div>

    
    <!-- South Section -->
    <div class="category">
        <h2>South</h2>
        <div class="places-container">
            <div class="place">
                <a href="yercaud.html">
                    <img src="yercaud lake.jpg">
                    <p>Yercaud</p>
                </a>
            </div>
            <div class="place">
                <a href="Munnar.html">
                    <img src="munnar.jpg">
                    <p>Munnar</p>
                </a>
            </div>
	<div class="place">
                <a href="vagoman.html">
                    <img src="images/Vagamon/images.jpg">
                    <p>Vagoman</p>
                </a>
            </div>

			<div class="place">
                <a href="wayanad.html">
                    <img src="Wayanad.jpg">
                    <p>Wayanad</p>
                </a>
            </div>

    <div class="place">
                <a href="yelagiri.html">
                    <img src="images/Yelagiri/Yelagiri_result.webp">
                    <p>Yelagiri</p>
                </a>
            </div>

	   
        </div>
    </div>

    
</body>
</html>