<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

// Join review details with food item name and user name
$query = "
    SELECT r.*, f.name AS food_name, u.username 
    FROM reviews r
    JOIN food_items f ON r.food_id = f.id
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Customer Reviews - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4 mb-4">
    <a class="navbar-brand text-warning" href="dashboard.php">🔧 Admin Panel</a>
    <div class="ms-auto">
        <a href="dashboard.php" class="btn btn-outline-light me-2">🏠 Dashboard</a>
        <a href="../logout.php" class="btn btn-outline-light">🚪 Logout</a>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">📝 Customer Reviews</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Food Item</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['food_name']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= str_repeat('⭐', $row['rating']) ?> (<?= $row['rating'] ?>)</td>
                            <td><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No reviews submitted yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
