<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camping Travel Website</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin-top: 0px;
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
        filter: blur(50px);
        /* Apply blur effect */
        z-index: -1;
        /* Place it behind the content */
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
        height: 75%;
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
$dbname = "6thsemproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
    <?php
//$a= $_POST['Name'];

if(isset($_POST['sign']))
{
$b= $_POST['Email'];   
$f= $_POST['Password']; 

$aa="Select * from registration_signup where Email= '$b' and Password= '$f' ";
$bb= mysqli_query($conn,$aa);
$cc = mysqli_fetch_assoc($bb);
$c = mysqli_num_rows($bb);
if($c>0)
{
?>
    <a href="update_signin.php?Name=<?php echo $cc["Name"] ?>">Update SignIn</a>

    <center>
        <h1><b>North</b></h1>
    </center>
    <?php
$sql2="select * from table2 where Direction='North' ";
$result2 = mysqli_query($conn,$sql2);
$c = mysqli_num_rows($result2);
$d=$c/3;

?>
    <center>
        <table width="1380" border="0">
            <tr>
                <td>
                    <center>
                        <table width="1370" border="1">
                            <?php 
for($i=1;$i<=$d;$i++)
{
$p=1;
?>
                            <tr>
                                <?php
while($row2 = mysqli_fetch_assoc($result2))
{
if($p<=4)
{
?>
                                <td width="195" height="250">
                                    <center><a href="hotels4.php?area=<?php echo $row2["Area"] ?>"><img
                                                src="<?php echo $row2["Images"]; ?>" width="190" height="150"
                                                border="2"></a><br><br>
                                        <a href="<?php echo $row2["Hyperlink"]; ?>"><input type="text" size="5"
                                                value="<?php echo $row2["Area"]; ?>"></a>
                                    </center>
                                </td>
                                <?php
$p++;
}
else
{
?>
                                <td width="195" height="250">
                                    <center><a href="<?php echo $row2["Hyperlink"]; ?>"><img
                                                src="<?php echo $row2["Images"]; ?>" width="190" height="150"
                                                border="2"></a><br><br>
                                        <input type="text" size="5" value="<?php echo $row2["Area"]; ?>">
                                    </center>
                                </td>
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
    <br><br>


    <center>
        <h1><b>South</b></h1>
    </center>
    <?php
$sql3="select * from table2 where Direction='South'" ;
$result3 = mysqli_query($conn,$sql3);
$f = mysqli_num_rows($result3);
$e=$f/2;

?>
    <center>
        <table width="1380" border="0">
            <tr>
                <td>
                    <center>
                        <table width="1370" border="1">
                            <?php 
for($i=1;$i<=$e;$i++)
{
$p=1;
?>
                            <tr>
                                <?php
while($row3 = mysqli_fetch_assoc($result3))
{
if($p<=4)
{
?>
                                <td width="195" height="250">
                                    <center><a href="<?php echo $row3["Hyperlink"]; ?>"><img
                                                src="<?php echo $row3["Images"]; ?>" width="190" height="150"
                                                border="2"></a><br><br>
                                        <a href="<?php echo $row3["Hyperlink"]; ?>"><input type="text" size="5"
                                                value="<?php echo $row3["Area"]; ?>"></a>
                                    </center>
                                </td>
                                <?php
$p++;
}
else
{
?>
                                <td width="195" height="200">
                                    <center><a href="<?php echo $row3["Hyperlink"]; ?>"><img
                                                src="<?php echo $row3["Images"]; ?>" width="190" height="150"
                                                border="2"></a><br><br>
                                        <input type="text" size="5" value="<?php echo $row3["Area"]; ?>">
                                    </center>
                                </td>
                                <?php
break;
}
}
?>
                            </tr>
                            <?php
}
?>
                            <?php

}
else
{
    // Show alert message then redirect to sign up page
    echo "<script>alert('Account does not exist. Please create a new account.'); window.location.href='sign.php';</script>";
    exit();
}
}
?>
                        </table>
                    </center>
                </td>
            </tr>
        </table>
    </center>

</body>

</html>