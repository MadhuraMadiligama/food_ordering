<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$user_id = $_SESSION["user_id"];

// Get user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Get cart count for the navbar badge
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders - FoodZone</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Icons for new navbar -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* General Body Styling */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Consistent light background */
            padding-top: 70px; /* Navbar එකට ඉඩ තැබීමට */
        }
        
        /* ========= NEW NAVIGATION BAR STYLING ========= */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 2rem;
            font-weight: 500;
        }
        .navbar-custom .navbar-brand {
            font-weight: 700;
            color: #ffc107 !important;
        }
        .navbar-custom .nav-link {
            color: #555 !important;
            margin: 0 0.5rem;
            transition: color 0.2s;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: #ffc107 !important;
        }
        .navbar-custom .cart-link {
            position: relative;
        }
        .navbar-custom .cart-count-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.7rem;
            padding: 0.2em 0.5em;
        }
        /* ========= END OF NAVBAR STYLING ========= */

        /* Your original card styling */
        .card {
            border-left: 5px solid #ffc107;
        }
    </style>
</head>
<body class="bg-light">

<!-- ========= UPDATED NAVIGATION BAR ========= -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="menu.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link active" href="track_order.php"><i class="bi bi-truck"></i> Track Orders</a></li>
                <li class="nav-item">
                    <a class="nav-link cart-link" href="cart.php">
                        <i class="bi bi-cart3"></i> Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="badge rounded-pill bg-danger cart-count-badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-sm btn-outline-danger ms-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- ========= END OF UPDATED NAVIGATION BAR ========= -->


<!-- The rest of your page content remains exactly as you provided it -->
<div class="container mt-4">
    <h3>📦 Track Your Orders</h3>

    <?php if ($orders_result->num_rows === 0): ?>
        <div class="alert alert-info mt-4">You haven't placed any orders yet.</div>
    <?php else: ?>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <?php
            $order_id = $order["id"];
            $order_notes = $order["notes"] ?? '';
            $order_date = date("d M Y, h:i A", strtotime($order["order_date"]));

            // Get order items
            $item_stmt = $conn->prepare("
                SELECT oi.*, fi.name 
                FROM order_items oi 
                JOIN food_items fi ON oi.food_id = fi.id 
                WHERE oi.order_id = ?
            ");
            $item_stmt->bind_param("i", $order_id);
            $item_stmt->execute();
            $items_result = $item_stmt->get_result();

            $total = 0;
            $items = [];
            while ($item = $items_result->fetch_assoc()) {
                $item_total = $item['price'] * $item['quantity'];
                $total += $item_total;
                $items[] = $item;
            }
            ?>

            <div class="card my-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        🧾 Order #<?= $order_id ?> 
                        <span class="badge bg-secondary ms-2"><?= ucfirst($order['status']) ?></span>
                    </h5>
                    <p class="mb-1"><strong>🕒 Date:</strong> <?= $order_date ?></p>
                    <?php if (!empty($order_notes)): ?>
                        <p class="mb-1"><strong>📝 Notes:</strong> <?= nl2br(htmlspecialchars($order_notes)) ?></p>
                    <?php endif; ?>
                    <ul class="mb-2">
                        <?php foreach ($items as $item): ?>
                            <li><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?> - 
                                Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <strong>Total: Rs. <?= number_format($total, 2) ?></strong>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>