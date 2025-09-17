<?php
$servername = "loan-app.c1a2c80aoes9.ap-south-1.rds.amazonaws.com";
$username = "admin";
$password = "admin123";
$dbname = "quickloan_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>