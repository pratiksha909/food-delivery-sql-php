<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['customer_id'], $data['restaurant_id'], $data['items'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$customer_id = (int)$data['customer_id'];
$restaurant_id = (int)$data['restaurant_id'];
$items = $data['items'];

$stmt = $conn->prepare("INSERT INTO orders (customer_id, restaurant_id) VALUES (?, ?)");
$stmt->bind_param("ii", $customer_id, $restaurant_id);
$stmt->execute();
$order_id = $conn->insert_id;

foreach ($items as $item) {
    $item_id = (int)$item['item_id'];
    $qty = (int)$item['quantity'];

    $price_stmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
    $price_stmt->bind_param("i", $item_id);
    $price_stmt->execute();
    $price = $price_stmt->get_result()->fetch_assoc()['price'];

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
    $item_stmt->bind_param("iiid", $order_id, $item_id, $qty, $price);
    $item_stmt->execute();
}

echo json_encode(["status" => "success", "order_id" => $order_id]);
?>