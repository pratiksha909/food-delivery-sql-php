<?php
require '../db.php';
header('Content-Type: application/json');

$city = $_GET['city'] ?? '';

if ($city !== '') {
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE city = ?");
    $stmt->bind_param("s", $city);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM restaurants");
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>