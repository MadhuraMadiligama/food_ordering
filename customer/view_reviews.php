<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$food_id = isset($_GET['food_id']) ? (int)$_GET['food_id'] : 0;

// Get food item details
$stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$food = $stmt->get_result()->fetch_assoc();

if (!$food) {
    echo "Food item not found.";
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION["user_id"];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = trim($_POST['comment']);

    if ($rating > 0 && $rating <= 5 && $comment !== "") {
        $insert = $conn->prepare("INSERT INTO reviews (food_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insert->bind_param("iiis", $food_id, $user_id, $rating, $comment);
        $insert->execute();
    }
}

// Get all reviews
$reviews = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.food_id = ? ORDER BY r.created_at DESC");
$reviews->bind_param("i", $food_id);
$reviews->execute();
$review_result = $reviews->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($food['name']) ?> - Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 mb-4">
        <a class="navbar-brand text-warning" href="menu.php">🍽️ FoodZone</a>
        <div class="ms-auto">
            <a href="menu.php" class="btn btn-outline-light">⬅ Back to Menu</a>
        </div>
    </nav>

    <div class="container">
        <h2>⭐ Reviews for <?= htmlspecialchars($food['name']) ?></h2>

        <!-- Review Form -->
        <div class="card my-4">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1 to 5)</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">-- Choose --</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>

        <!-- Reviews List -->
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
            <div class="alert alert-info">No reviews yet for this item.</div>
        <?php endif; ?>
    </div>
</body>
</html>
