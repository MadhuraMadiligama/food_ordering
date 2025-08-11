<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include(__DIR__ . "/../includes/db.php");

// Fetch categories for dropdown
$category_result = $conn->query("SELECT * FROM categories");

// Handle add
if (isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $category_id = $_POST['category_id'];
    $stock = intval($_POST['stock']);

    if ($stock < 0) {
        $stock = 0;
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $target_dir = "../assets/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . time() . "_" . $img_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Use prepared statement
    $stmt = $conn->prepare("INSERT INTO food_items (name, price, description, image, category_id, stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssii", $name, $price, $desc, $image_path, $category_id, $stock);

    $stmt->execute();
    $stmt->close();

    header("Location: manage_food.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Check if food item is used in any orders
    $check = $conn->query("SELECT * FROM order_items WHERE food_id = $id");
    if ($check->num_rows > 0) {
        echo "<script>alert('Cannot delete: This food item is part of existing orders.'); window.location.href='manage_food.php';</script>";
        exit();
    }

    // Delete image from folder too
    $imgResult = $conn->query("SELECT image FROM food_items WHERE id=$id");
    if ($imgResult && $imgRow = $imgResult->fetch_assoc()) {
        if ($imgRow['image'] && file_exists($imgRow['image'])) {
            unlink($imgRow['image']);
        }
    }

    $conn->query("DELETE FROM food_items WHERE id=$id");

    header("Location: manage_food.php");
    exit();
}

// Get all food items with category names
$foods = $conn->query("SELECT f.*, c.name AS category_name 
                       FROM food_items f 
                       LEFT JOIN categories c ON f.category_id = c.id 
                       ORDER BY f.id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">



<div class="container mt-3">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['info_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['info_message']); ?>
    <?php endif; ?>
</div>



<div class="container mt-3">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): // Error messages තිබුණා නම් ඒවත් පෙන්වන්න ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>


<div class="container mt-5">
    <h2 class="mb-4">🍽️ Manage Food Items</h2>

    <!-- Add Form -->
    <div class="card text-dark mb-4 p-3">
        <h4>Add New Food Item</h4>
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3 mt-1">
                <div class="col-md-2">
                    <input type="number" name="stock" placeholder="Stock Qty" class="form-control" required min="0">
                </div>
                <div class="col-md-2">
                    <input type="text" name="name" placeholder="Food Name" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="description" placeholder="Description" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while ($cat = $category_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="file" name="image" class="form-control" accept="image/*">
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
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Actions &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit</th>
                    <!--<th>Edit</th>-->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $foods->fetch_assoc()): ?>
<tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>Rs. <?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars(substr($row['description'], 0, 50)) ?><?= (strlen($row['description']) > 50) ? '...' : '' ?></td>
                    <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
                    <td>
                        <?php if ((int)$row['stock'] > 0): ?>
                            <span class="badge bg-success"><?= (int)$row['stock'] ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger">0</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item? This action cannot be undone.')">🗑️ Delete</a>
                    <a href="edit_food.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white me-1">✏️ Edit</a>
                    </td>
                    
                </tr>
                <?php endwhile; ?>
                <?php if ($foods->num_rows === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted">No food items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
</div>
</body>
</html>
