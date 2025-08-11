<?php
session_start();

// Ensure only logged-in administrators can access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include your database connection
include("../includes/db.php");

// Fetch all reviews along with the username of the reviewer and the name of the food item
$sql = "SELECT r.*, u.username, f.name AS food_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN food_items f ON r.food_id = f.id
        ORDER BY r.created_at DESC"; // Order by newest reviews first

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark px-4 mb-4">
        <a class="navbar-brand text-warning" href="dashboard.php">🛠️ Admin Panel</a>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </nav>

    <div class="container">
        <h3 class="mb-4">📊 Customer Reviews</h3>

        <?php if ($result->num_rows > 0): // Check if there are any reviews to display ?>
            <table class="table table-striped table-hover mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Food Item</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($review = $result->fetch_assoc()): // Loop through each review ?>
                    <tr>
                        <td><?= $review['id'] ?></td>
                        <td><?= htmlspecialchars($review['food_name']) ?></td>
                        <td><?= htmlspecialchars($review['username']) ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= ($i <= $review['rating']) ? '⭐' : '☆' ?>
                            <?php endfor; ?>
                            (<?= $review['rating'] ?>/5)
                        </td>
                        <td><?= nl2br(htmlspecialchars($review['comment'])) ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($review['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info mt-4">No customer reviews found yet.</div>
        <?php endif; ?>
    </div>

</body>
</html>