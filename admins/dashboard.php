<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card text-dark shadow p-4">
            <h2 class="mb-4">👋 Welcome, Admin!</h2>
            <p>You are logged in as: <strong><?= $_SESSION["admin_email"] ?></strong></p>
            <div class="mt-4">
                <a href="manage_food.php" class="btn btn-warning me-2">🍔 Manage Food Items</a>
              <!-- <a href="view_food_reviews.php?food_id=<?= $row['id'] ?>" class="btn btn-outline-info btn-sm mt-2">
    ⭐ View Reviews
</a>-->



                <a href="manage_orders.php" class="btn btn-primary">📦 Manage Orders</a>
                <a href="report.php" class="btn btn-success me-2">📊 View Reports</a>
                <a href="view_users.php" class="btn btn-primary">View Registered Users</a>

                <a href="login.php" class="btn btn-danger float-end">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
