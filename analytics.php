<?php
require 'db.php';

// Query 1: Best-selling item per restaurant
$q1 = $conn->query("
    SELECT r.name AS restaurant, mi.name AS item, SUM(oi.quantity) AS total_qty,
           RANK() OVER (PARTITION BY r.id ORDER BY SUM(oi.quantity) DESC) AS rnk
    FROM order_items oi
    JOIN menu_items mi ON oi.item_id = mi.id
    JOIN restaurants r ON mi.restaurant_id = r.id
    GROUP BY r.id, mi.id
");

// Query 2: Average order value by city
$q2 = $conn->query("
    SELECT c.city, ROUND(AVG(order_total), 2) AS avg_order_value
    FROM (
        SELECT o.id, o.customer_id, SUM(oi.quantity * oi.price_at_order) AS order_total
        FROM orders o JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
    ) t
    JOIN customers c ON t.customer_id = c.id
    GROUP BY c.city
");

// Query 3: Peak ordering hours
$q3 = $conn->query("
    SELECT HOUR(order_date) AS hour_of_day, COUNT(*) AS order_count
    FROM orders
    GROUP BY hour_of_day
    ORDER BY order_count DESC
");

// Query 4: Restaurant revenue ranking
$q4 = $conn->query("
    SELECT r.name, SUM(oi.quantity * oi.price_at_order) AS revenue
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN restaurants r ON o.restaurant_id = r.id
    GROUP BY r.id
    ORDER BY revenue DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Analytics Dashboard</h1>
    <a href="restaurants.php">&larr; Back to Restaurants</a>

    <h2>1. Best-Selling Item per Restaurant</h2>
    <table>
        <tr><th>Restaurant</th><th>Item</th><th>Qty Sold</th><th>Rank</th></tr>
        <?php while ($row = $q1->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['restaurant']) ?></td>
            <td><?= htmlspecialchars($row['item']) ?></td>
            <td><?= $row['total_qty'] ?></td>
            <td><?= $row['rnk'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>2. Average Order Value by City</h2>
    <table>
        <tr><th>City</th><th>Avg Order Value</th></tr>
        <?php while ($row = $q2->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td>₹<?= $row['avg_order_value'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>3. Peak Ordering Hours</h2>
    <table>
        <tr><th>Hour of Day</th><th>Order Count</th></tr>
        <?php while ($row = $q3->fetch_assoc()): ?>
        <tr>
            <td><?= $row['hour_of_day'] ?>:00</td>
            <td><?= $row['order_count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>4. Restaurant Revenue Ranking</h2>
    <table>
        <tr><th>Restaurant</th><th>Revenue</th></tr>
        <?php while ($row = $q4->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>₹<?= number_format($row['revenue'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>