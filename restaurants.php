<?php
require 'db.php';

// Get filter values from the URL (if any)
$city = $_GET['city'] ?? '';
$min_rating = $_GET['min_rating'] ?? '';

// Build query dynamically based on filters
$sql = "SELECT * FROM restaurants WHERE 1=1";
$params = [];
$types = "";

if ($city !== '') {
    $sql .= " AND city = ?";
    $params[] = $city;
    $types .= "s";
}
if ($min_rating !== '') {
    $sql .= " AND rating >= ?";
    $params[] = $min_rating;
    $types .= "d";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurants</title>
</head>
<body>
    <h1>Restaurants</h1>

    <!-- Filter form -->
    <form method="GET">
        <label>City:</label>
        <select name="city">
            <option value="">All</option>
            <option value="Mumbai" <?= $city == 'Mumbai' ? 'selected' : '' ?>>Mumbai</option>
            <option value="Pune" <?= $city == 'Pune' ? 'selected' : '' ?>>Pune</option>
            <option value="Bangalore" <?= $city == 'Bangalore' ? 'selected' : '' ?>>Bangalore</option>
            <option value="Delhi" <?= $city == 'Delhi' ? 'selected' : '' ?>>Delhi</option>
        </select>

        <label>Min Rating:</label>
        <input type="number" step="0.1" name="min_rating" value="<?= htmlspecialchars($min_rating) ?>">

        <button type="submit">Filter</button>
    </form>

    <!-- Results table -->
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>City</th>
            <th>Rating</th>
            <th>Menu</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td><?= $row['rating'] ?></td>
            <td><a href="menu.php?restaurant_id=<?= $row['id'] ?>">View Menu</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>