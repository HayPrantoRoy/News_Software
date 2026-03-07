<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news_software";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Important: set charset for Bangla/Unicode support
$conn->set_charset("utf8mb4");
?>
