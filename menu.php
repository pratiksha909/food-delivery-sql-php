<?php
require 'db.php';

$restaurant_id = $_GET['restaurant_id'] ?? 0;

// Get restaurant name for the heading
$stmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();

// Get menu items for this restaurant, grouped conceptually by category
$stmt2 = $conn->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? ORDER BY category, name");
$stmt2->bind_param("i", $restaurant_id);
$stmt2->execute();
$items = $stmt2->get_result();
?>

<!DOCTYPE html>
<head>
    <title>Restaurants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Menu — <?= htmlspecialchars($restaurant['name'] ?? 'Unknown Restaurant') ?></h1>
    <a href="restaurants.php">&larr; Back to Restaurants</a>

    <table border="1" cellpadding="8">
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Price</th>
        </tr>
        <?php while ($row = $items->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>₹<?= number_format($row['price'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>