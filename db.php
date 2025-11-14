<?php
$servername = "127.0.0.1";
$username = "root";
$password = "1106";
$dbname = "library";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
