<?php
require 'db.php';

$restaurant_id = $_GET['restaurant_id'] ?? '';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $restaurant_id = $_POST['restaurant_id'];
    $item_ids = $_POST['item_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    // Insert the order first
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, restaurant_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $customer_id, $restaurant_id);
    $stmt->execute();
    $order_id = $conn->insert_id; // grabs the auto-generated order ID

    // Insert each selected item into order_items
    foreach ($item_ids as $index => $item_id) {
        $qty = (int)$quantities[$index];
        if ($qty > 0) { // skip items left at 0
            $price_stmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
            $price_stmt->bind_param("i", $item_id);
            $price_stmt->execute();
            $price = $price_stmt->get_result()->fetch_assoc()['price'];

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
            $item_stmt->bind_param("iiid", $order_id, $item_id, $qty, $price);
            $item_stmt->execute();
        }
    }

    $message = "Order #$order_id placed successfully!";
}

// Get all restaurants for the dropdown
$restaurants = $conn->query("SELECT * FROM restaurants");

// Get all customers for the dropdown
$customers = $conn->query("SELECT * FROM customers");

// If a restaurant is selected, get its menu items
$menu_items = null;
if ($restaurant_id !== '') {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $menu_items = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Place an Order</h1>
    <a href="restaurants.php">&larr; Back to Restaurants</a>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <!-- Step 1: choose restaurant (reloads page to show its menu) -->
    <form method="GET">
        <label>Restaurant:</label>
        <select name="restaurant_id" onchange="this.form.submit()">
            <option value="">-- Select a Restaurant --</option>
            <?php while ($r = $restaurants->fetch_assoc()): ?>
                <option value="<?= $r['id'] ?>" <?= $restaurant_id == $r['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['city']) ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($menu_items && $menu_items->num_rows > 0): ?>
    <!-- Step 2: pick customer + items, then submit the actual order -->
    <form method="POST">
        <input type="hidden" name="restaurant_id" value="<?= $restaurant_id ?>">

        <label>Customer:</label>
        <select name="customer_id" required>
            <?php $customers->data_seek(0); // reset pointer since we already used it once above ?>
            <?php while ($c = $customers->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <h3>Select items:</h3>
        <table border="1" cellpadding="8">
            <tr><th>Item</th><th>Price</th><th>Quantity</th></tr>
            <?php while ($item = $menu_items->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>₹<?= $item['price'] ?></td>
                <td>
                    <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>">
                    <input type="number" name="quantity[]" value="0" min="0" style="width: 60px;">
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <button type="submit">Place Order</button>
    </form>
    <?php endif; ?>
</body>
</html>