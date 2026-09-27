<?php
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $city = $_POST['city'];

    $stmt = $conn->prepare("INSERT INTO customers (name, city) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $city);
    $stmt->execute();

    $message = "Customer '$name' added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
    <h1>Add New Customer</h1>
    <a href="restaurants.php">&larr; Back to Restaurants</a>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br><br>

        <label>City:</label>
        <select name="city" required>
            <option value="Mumbai">Mumbai</option>
            <option value="Pune">Pune</option>
            <option value="Bangalore">Bangalore</option>
            <option value="Delhi">Delhi</option>
        </select><br><br>

        <button type="submit">Add Customer</button>
    </form>
</div>
</body>
</html>