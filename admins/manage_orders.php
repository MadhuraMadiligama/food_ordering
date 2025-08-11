<?php
session_start();
include '../includes/db.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders with correct column name
$query = "SELECT orders.id, orders.order_date, orders.status, users.name AS customer_name, orders.total
          FROM orders
          JOIN users ON orders.user_id = users.id
          ORDER BY orders.order_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>📦 Manage Orders</h2>

<a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>

    <table class="table table-bordered mt-3 bg-white">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td>Rs. <?= number_format($row['total'], 2) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= $row['order_date'] ?></td>
                <td>
                    <a href="view_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a>
                    <a href="update_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Update</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

     <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
</div>
</body>
</html>
