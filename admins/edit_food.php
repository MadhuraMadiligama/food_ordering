<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}
include("../includes/db.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid food ID";
    exit();
}

// Get existing food item
$stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];
    $stock = intval($_POST["stock"]); // Stock update එකත් මෙතන තියෙනවා

    if ($stock < 0) {
        $stock = 0;
    }

    $image_path = $item['image']; // මුලින්ම database එකේ දැනට තියෙන image path එක ගන්නවා

    // --- Image Upload Handling ---
    // අලුත් image file එකක් upload වෙලාද කියලා check කරනවා
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $target_dir = "../assets/uploads/"; // මේ directory එක තියෙනවද සහ write කරන්න පුළුවන්ද කියලා check කරන්න
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // directory එක නැත්නම් හදනවා
        }
        $target_file = $target_dir . time() . "_" . $img_name; // unique filename එකක් හදන්න timestamp එකක් එකතු කරනවා

        // Upload කරපු file එක move කරන්න උත්සාහ කරනවා
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // අලුත් image එක සාර්ථකව upload වුණා නම්, පරණ image එක delete කරන්න
            if ($image_path && file_exists($image_path)) {
                unlink($image_path); // පරණ image file එක delete කරනවා
            }
            $image_path = $target_file; // database එකට save කරන්න අලුත් image path එක update කරනවා
        } else {
            // අලුත් image එක move කරන්න බැරි වුණා නම්, error message එකක් set කරනවා
            $_SESSION['error_message'] = "Error uploading new image.";
            // මේ වෙලාවේ manage_food.php එකට redirect කරන්න හෝ image එක update නොකර continue කරන්න පුළුවන්
        }
    }
    // --- Image Upload Handling අවසන් ---

    // දැන් database එක update කරන්න, අලුත් (හෝ පරණ) image path එකත් එක්ක
    $stmt = $conn->prepare("UPDATE food_items SET name=?, description=?, price=?, category_id=?, stock=?, image=? WHERE id=?");
    // bind_param එකට image_path එකට අදාල 's' (string) එකතු කරනවා
$stmt->bind_param("ssdiisi", $name, $description, $price, $category_id, $stock, $image_path, $id);    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = htmlspecialchars($name) . " updated successfully!";
    } else {
        // කිසිදු වෙනසක් නොවූ විට හෝ update එක අසාර්ථක වූ විට
        $_SESSION['info_message'] = "No changes made to " . htmlspecialchars($name) . " or update failed.";
    }
    
    $stmt->close();

    header("Location: manage_food.php");
    exit();
}

// Get categories
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Food Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">


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



<div class="container mt-5">
    <h3>Edit Food Item</h3>
   <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Price:</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $item['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Stock Quantity:</label>
            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($item['stock']) ?>" required min="0">
        </div>
        <div class="mb-3">
            <label>Category:</label>
            <select name="category_id" class="form-select">
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $item['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>


<div class="mb-3">
            <label>Current Image:</label><br>
            <?php if ($item['image'] && file_exists($item['image'])): ?>
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Current Food Image" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                <p class="text-muted mt-1">Leave blank to keep current image.</p>
            <?php else: ?>
                <p class="text-muted">No current image or image not found.</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Upload New Image (Optional):</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>


        <button type="submit" class="btn btn-success">💾 Update</button>
        <a href="manage_food.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
