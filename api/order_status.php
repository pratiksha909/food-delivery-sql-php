<?php
require '../db.php';
header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(["error" => "Order not found"]);
    exit;
}

$stmt2 = $conn->prepare("SELECT mi.name, oi.quantity, oi.price_at_order 
                         FROM order_items oi 
                         JOIN menu_items mi ON oi.item_id = mi.id 
                         WHERE oi.order_id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$items_result = $stmt2->get_result();

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

$order['items'] = $items;
echo json_encode($order);
?>