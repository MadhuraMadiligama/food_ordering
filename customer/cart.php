<?php
session_start();
include("../includes/db.php"); 


if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


if (isset($_GET['remove_id'])) {
    $remove_food_id = (int)$_GET['remove_id'];

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $cart_item) {
            if ($cart_item['id'] == $remove_food_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
        $_SESSION['success_message'] = "Item removed from cart successfully!";
    } else {
        $_SESSION['error_message'] = "Your cart is already empty.";
    }
    header("Location: cart.php");
    exit();
}

$grand_total = 0;
foreach ($_SESSION["cart"] as $item) {
    $grand_total += $item["price"] * $item["quantity"];
}

$cart_count = count($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Your Cart</title>
    <!-- Google Fonts & Bootstrap (Unchanged) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons for new navbar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* General Body Styling (Unchanged) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #333333;
            min-height: 100vh;
            margin: 0;
            padding-top: 70px; /* Navbar එකට ඉඩ තැබීමට */
            box-sizing: border-box;
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


        /* Your other styles remain unchanged */
        .cart-container {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 960px;
            width: 100%;
            margin: 40px auto;
            border: 1px solid #e0e0e0;
        }
        h2 {
            color: #333333;
            font-weight: 700;
            margin-bottom: 30px;
        }
        .table {
            color: #333333;
            margin-bottom: 30px;
        }
        /* ... (all your other original styles are here and unchanged) ... */
        .img-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
        .btn-custom {
            background-color: #ffc107;
            border: none;
            color: #1a1a1a;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-custom:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            color: #1a1a1a;
        }
        .btn-outline-secondary {
            background-color: #ffffff;
            border-color: #ced4da;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-outline-secondary:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
            color: #333333;
        }
        .btn-danger-custom {
            background-color: #dc3545;
            border: none;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }
        .btn-danger-custom:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        /* ... etc ... */
    </style>
</head>
<body>

<!-- ========= UPDATED NAVIGATION BAR ========= -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top"> <!-- Added fixed-top -->
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="menu.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="track_order.php"><i class="bi bi-truck"></i> Track Orders</a></li>
                <li class="nav-item">
                    <!-- Cart link is now 'active' on the cart page -->
                    <a class="nav-link cart-link active" href="cart.php"> 
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
<div class="cart-container">
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

    <h2 class="mb-4">🛒 Your Cart</h2>
    <a href="menu.php" class="btn btn-outline-secondary mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
        </svg>
        Back to Menu
    </a>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Food Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0; 
                    foreach ($_SESSION["cart"] as $key => $item):
                        $sub_total = $item["price"] * $item["quantity"];
                        $grand_total += $sub_total;
                        $imgPath = "../assets/uploads/" . basename($item['image']); 
                        if (!file_exists($imgPath) || empty($item['image'])) {
                            $imgPath = "https://placehold.co/80x80/cccccc/333333?text=No+Img";
                        }
                    ?>
                    <tr>
                        <td>
                            <img src="<?= $imgPath ?>" class="img-thumb me-2" alt="<?= htmlspecialchars($item['name']) ?>">
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td>Rs. <?= number_format($item["price"], 2) ?></td>
                        <td><span class="fw-bold"><?= (int)$item["quantity"] ?></span></td>
                        <td>Rs. <?= number_format($sub_total, 2) ?></td>
                        <td>
                            <a href="cart.php?remove_id=<?= $item['id'] ?>" class="btn btn-danger-custom" onclick="return confirm('Are you sure you want to remove <?= htmlspecialchars($item['name']) ?> from your cart?');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                </svg>
                                Remove
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                        <td colspan="2" class="fw-bold fs-5">Rs. <?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <a href="checkout.php" class="btn btn-custom btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag-check-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0m-3.914 9.914a.5.5 0 0 0 .708-.708L7.5 11.293l4.646-4.647a.5.5 0 0 0-.708-.708L7.5 10.586 5.854 8.94a.5.5 0 1 0-.708.708l2.354 2.353Z"/>
                </svg>
                Proceed to Checkout
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>