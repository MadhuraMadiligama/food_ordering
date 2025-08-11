<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// This variable is used for the cart count badge in the navbar
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <!-- Google Fonts, Icons, and Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* General body style */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
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

        /* Welcome message styling (from your original code) */
        .welcome-header h2 {
            font-weight: 700;
            color: #333;
        }

        /* Large Action Cards styling (from previous step) */
        .large-action-card {
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            padding: 2.5rem;
            height: 100%;
        }
        .large-action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .large-action-card .card-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ffc107;
        }
        .large-action-card .card-title {
            font-size: 2rem;
            font-weight: 600;
        }
        .large-action-card .card-text {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        .large-action-card .btn {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- ========= UPDATED NAVIGATION BAR ========= -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="index.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="track_order.php"><i class="bi bi-truck"></i> Track Orders</a></li>
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

<div class="container mt-5">
    <!-- Welcome message section (Unchanged) -->
    <div class="d-flex justify-content-between align-items-center mb-4 welcome-header">
        <h2>Hello, <?= htmlspecialchars($_SESSION["user_name"] ?? 'User') ?> 👋</h2>
        <!-- Logout button is now in the navbar, so we can remove it from here if we want -->
        <!-- <a href="../logout.php" class="btn btn-dark">Logout</a> -->
    </div>

    <!-- Action cards section (Unchanged) -->
    <div class="row">
        <!-- Menu Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card large-action-card">
                <div class="card-body d-flex flex-column">
                    <div class="card-icon">🍔</div>
                    <h4 class="card-title mt-3">Menu</h4>
                    <p class="card-text">Browse all food items</p>
                    <a href="menu.php" class="btn btn-warning mt-auto">View Menu</a>
                </div>
            </div>
        </div>

        <!-- Cart Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card large-action-card">
                <div class="card-body d-flex flex-column">
                    <div class="card-icon">🛒</div>
                    <h4 class="card-title mt-3">Cart</h4>
                    <p class="card-text">See items in your cart</p>
                    <a href="cart.php" class="btn btn-warning mt-auto">View Cart</a>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card large-action-card">
                <div class="card-body d-flex flex-column">
                    <div class="card-icon">📦</div>
                    <h4 class="card-title mt-3">Orders</h4>
                    <p class="card-text">Track your past orders</p>
                    <a href="track_order.php" class="btn btn-warning mt-auto">View Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>