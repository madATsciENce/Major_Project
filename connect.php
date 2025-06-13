<?php
$mysqli = new mysqli("localhost", "root", "", "6thsemproject");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
