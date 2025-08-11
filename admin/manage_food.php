<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include("../includes/db_connect.php");


// Handle add
if (isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $conn->query("INSERT INTO food_items (name, price, description) VALUES ('$name', '$price', '$desc')");
    header("Location: manage_food.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM food_items WHERE id=$id");
    header("Location: manage_food.php");
    exit();
}

// Get all items
$foods = $conn->query("SELECT * FROM food_items ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2 class="mb-4">🍽️ Manage Food Items</h2>

    <!-- Add Form -->
    <div class="card text-dark mb-4 p-3">
        <h4>Add New Food Item</h4>
        <form method="post">
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <input type="text" name="name" placeholder="Food Name" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="description" placeholder="Short Description" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_food" class="btn btn-warning w-100">➕ Add</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Food Table -->
    <div class="card text-dark p-3">
        <h4 class="mb-3">📋 All Food Items</h4>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Price (Rs.)</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $foods->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($foods->num_rows === 0): ?>
                    <tr><td colspan="4" class="text-center text-muted">No food items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
