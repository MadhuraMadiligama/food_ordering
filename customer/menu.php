<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

// Get all categories for the filter
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// --- IMPORTANT: Use Prepared Statements for security ---
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : null;

// Base query
$query = "SELECT f.*, c.name as category_name FROM food_items f 
          LEFT JOIN categories c ON f.category_id = c.id";
$conditions = [];
$params = [];
$types = '';

// Add category filter
if ($category_id) {
    $conditions[] = "f.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

// Add search filter
if ($search_term) {
    $conditions[] = "(f.name LIKE ? OR f.description LIKE ?)";
    $like_search_term = "%" . $search_term . "%";
    $params[] = $like_search_term;
    $params[] = $like_search_term;
    $types .= 'ss';
}

// Append conditions to the query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY f.name ASC";

// Prepare and execute the statement
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
// --- End of secure query preparation ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Our Menu</title>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* General Body Styling (Consistent with other pages) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light gray background for the whole page */
            color: #333333;
        }

        /* --- Navigation Bar --- */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 2rem;
        }
        .navbar-brand {
            font-weight: 700;
            color: #ffc107 !important;
        }
        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffc107 !important;
        }
        .btn-logout {
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
            font-weight: 500;
        }
        .btn-logout:hover {
            background-color: #e5c2c5;
            color: #842029;
            border-color: #f5c2c7;
        }
        .cart-link {
            position: relative;
        }
        .cart-count-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.7rem;
            padding: 0.2em 0.5em;
        }


        /* --- Page Header & Filters --- */
        .page-header {
            background-color: #ffffff;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e0e0e0;
        }
        .search-form .form-control {
            border-radius: 50px;
            padding-left: 2.5rem;
            border: 1px solid #ced4da;
        }
        .search-form .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .category-filter .btn {
            border-radius: 50px;
            font-weight: 500;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }
        .category-filter .btn.active {
            background-color: #ffc107;
            color: #333;
            border-color: #ffc107;
        }

        /* --- Food Item Card --- */
        .food-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden; /* Ensures image corners are rounded */
        }
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-title {
            font-weight: 600;
        }
        .card-text {
            font-size: 0.9rem;
            color: #666;
            flex-grow: 1; /* Pushes content below it to the bottom */
        }
        .category-badge {
            background-color: #e9ecef;
            color: #333;
            font-weight: 500;
            padding: 0.4em 0.8em;
        }
        .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
        }
        .btn-add-to-cart {
            background-color: #ffc107;
            color: #1a1a1a;
            font-weight: 600;
        }
        .btn-add-to-cart:hover {
            background-color: #e0a800;
            color: #1a1a1a;
        }

        /* Alert styling for light theme */
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="menu.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="track_order.php"><i class="bi bi-truck"></i> Track Orders</a></li>
                <li class="nav-item">
                    <a class="nav-link cart-link" href="cart.php">
                        <i class="bi bi-cart3"></i> Cart
                        <?php 
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cart_count > 0): 
                        ?>
                            <span class="badge rounded-pill bg-danger cart-count-badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item"><a href="../logout.php" class="btn btn-sm btn-logout ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header with Title, Search and Filters -->
<div class="page-header">
    <div class="container">
        <h1 class="display-5 fw-bold text-center mb-4">🍴 Our Menu</h1>
        
        <!-- Search and Filter Row -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <form method="GET" action="menu.php" class="search-form position-relative mb-4">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search_term ?? '') ?>" class="form-control form-control-lg" placeholder="Search by name or description...">
                    <?php if ($category_id): // Keep category filter when searching ?>
                        <input type="hidden" name="category" value="<?= $category_id ?>">
                    <?php endif; ?>
                </form>

                <!-- Category Filter -->
                <div class="category-filter text-center">
                    <a href="menu.php" class="btn btn-outline-secondary <?= !$category_id ? 'active' : '' ?>">All Items</a>
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                        <a href="menu.php?category=<?= $cat['id'] ?><?= $search_term ? '&search=' . urlencode($search_term) : '' ?>" 
                           class="btn btn-outline-secondary <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container my-4">
    <!-- Alert Messages -->
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

    <!-- Food Items Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $imageFile = basename($row['image']);
                $imgSrc = file_exists("../assets/uploads/" . $imageFile) ? "../assets/uploads/" . $imageFile : "../assets/uploads/default-food.png"; // Fallback image
            ?>
            <div class="col">
                <div class="card food-card h-100">
                    <img src="<?= $imgSrc ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                    <div class="card-body p-4">
                        <?php if ($row['category_name']): ?>
                            <span class="badge category-badge mb-2">
                                <?= htmlspecialchars($row['category_name']) ?>
                            </span>
                        <?php endif; ?>
                        <h5 class="card-title mt-1"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="card-text mb-3"><?= htmlspecialchars($row['description']) ?></p>
                        
<div class="mt-auto"> <!-- This div will be pushed to the bottom -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="price mb-0">Rs. <?= number_format($row['price'], 2) ?></p>
        <!-- Stock count is now explicitly shown -->
        <p class="mb-0">
            <strong>Stock:</strong> 
            <?php if ($row['stock'] > 0): ?>
                <span class="badge bg-success-subtle text-success-emphasis rounded-pill p-2">
                    <?= (int)$row['stock'] ?> Available
                </span>
            <?php else: ?>
                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill p-2">
                    Out of Stock
                </span>
            <?php endif; ?>
        </p>
    </div>

    <div class="d-grid gap-2 mt-3">
        <?php if ($row['stock'] > 0): ?>
            <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-add-to-cart"><i class="bi bi-basket-fill"></i> Add to Cart</a>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>Out of Stock</button>
        <?php endif; ?>
        <a href="view_item.php?food_id=<?= $row['id'] ?>" class="btn btn-outline-secondary">View Details</a>
    </div>
</div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="fs-4 text-muted">No menu items found matching your criteria.</p>
                <a href="menu.php" class="btn btn-primary">Back to Full Menu</a>
            </div>
        <?php endif; ?>
        <?php $stmt->close(); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>