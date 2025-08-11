<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$food_id = isset($_GET['food_id']) ? (int)$_GET['food_id'] : 0;

// Get food item info
$stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$food = $stmt->get_result()->fetch_assoc();

if (!$food) {
    echo "Food item not found.";
    exit();
}

// Get reviews with user info
$reviews = $conn->prepare("
    SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.food_id = ? 
    ORDER BY r.created_at DESC
");
$reviews->bind_param("i", $food_id);
$reviews->execute();
$review_result = $reviews->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - <?= htmlspecialchars($food['name']) ?> Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 mb-4">
        <a class="navbar-brand text-warning" href="dashboard.php">🛠️ Admin Panel</a>
        <div class="ms-auto">
            <a href="manage_food.php" class="btn btn-outline-light">⬅ Back</a>
        </div>
    </nav>

    <div class="container">
        <h2>⭐ Customer Reviews for <span class="text-primary"><?= htmlspecialchars($food['name']) ?></span></h2>

        <?php if ($review_result->num_rows > 0): ?>
            <?php while ($r = $review_result->fetch_assoc()): ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-body">
                        <h6><?= htmlspecialchars($r['username']) ?> | <?= $r['rating'] ?> ⭐</h6>
                        <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                        <small class="text-muted"><?= $r['created_at'] ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info mt-3">No reviews yet for this item.</div>
        <?php endif; ?>
    </div>
</body>
</html>
