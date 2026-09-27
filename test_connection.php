<?php
require 'db.php';
echo "Connected successfully to the database!";

$result = $conn->query("SELECT COUNT(*) AS total FROM restaurants");
$row = $result->fetch_assoc();
echo "<br>Number of restaurants in DB: " . $row['total'];
?>