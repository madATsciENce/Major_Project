<?php
$mysqli=new mysqli("localhost","root","","Project");
if ($mysqli -> connect_errno)
{
echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
exit();
}
?>
