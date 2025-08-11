<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$food_id = isset($_GET['food_id']) ? (int)$_GET['food_id'] : 0;

if ($food_id <= 0) {
    header("Location: menu.php");
    exit();
}

$stmt = $conn->prepare("SELECT f.*, c.name AS category_name FROM food_items f LEFT JOIN categories c ON f.category_id = c.id WHERE f.id = ?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();
$food = $result->fetch_assoc();
$stmt->close();

if (!$food) {
    $_SESSION['error_message'] = "The food item you're looking for was not found.";
    header("Location: menu.php");
    exit();
}

$imageFile = basename($food['image']);
$imgSrc = file_exists("../assets/uploads/" . $imageFile) && !empty($food['image']) ? "../assets/uploads/" . $imageFile : "../assets/uploads/default-food.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($food['name']) ?> - FoodZone Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 2rem;
        }
        .navbar-brand { font-weight: 700; color: #ffc107 !important; }
        .nav-link { color: #555 !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: #ffc107 !important; }
        .details-container {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.07);
        }
        .food-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 12px;
        }
        .food-title {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .category-badge {
            background-color: #ffc107 !important;
            color: #333 !important;
            font-weight: 500;
            padding: 0.5em 1em;
            font-size: 0.9rem;
        }
        .food-description {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.7;
        }
        .price-tag {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
        }
        .quantity-selector .form-control {
            border-left: none;
            border-right: none;
            border-radius: 0;
            box-shadow: none;
            /* Make sure the input field itself doesn't shrink */
            min-width: 60px; /* Adjust this value as needed */
        }
        .quantity-selector .btn {
            border-radius: 8px 0 0 8px;
        }
        .quantity-selector .btn:last-child {
            border-radius: 0 8px 8px 0;
        }
        .btn-add-to-cart {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #1a1a1a;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }
        .btn-add-to-cart:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #1a1a1a;
        }
        .btn-view-reviews {
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="menu.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <div class="d-flex align-items-center">
            <a class="nav-link me-3" href="cart.php"><i class="bi bi-cart3"></i> Cart</a>
            <a href="menu.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Menu</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="details-container">
        <div class="row g-5 align-items-center">
            <!-- Image Column -->
            <div class="col-lg-6">
                <img src="<?= $imgSrc ?>" class="food-image" alt="<?= htmlspecialchars($food['name']) ?>">
            </div>

            <!-- Details Column -->
            <div class="col-lg-6">
                <?php if ($food['category_name']): ?>
                    <span class="badge category-badge mb-3"><?= htmlspecialchars($food['category_name']) ?></span>
                <?php endif; ?>
                <h1 class="food-title"><?= htmlspecialchars($food['name']) ?></h1>
                
                <p class="food-description my-4"><?= nl2br(htmlspecialchars($food['description'])) ?></p>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <p class="price-tag mb-0">Rs. <?= number_format($food['price'], 2) ?></p>
                    <p class="mb-0">
                        <strong>Stock:</strong> 
                        <?php if ($food['stock'] > 0): ?>
                            <span class="badge bg-success p-2"><?= (int)$food['stock'] ?> Available</span>
                        <?php else: ?>
                            <span class="badge bg-danger p-2">Out of Stock</span>
                        <?php endif; ?>
                    </p>
                </div>

                <hr class="my-4">

                <!-- Add to Cart Form -->
                <?php if ($food['stock'] > 0): ?>
                    <form action="add_to_cart.php" method="GET">
                        <input type="hidden" name="id" value="<?= $food['id'] ?>">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="qty" class="form-label mb-0">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <!-- ========= UPDATED PART HERE ========= -->
                                <!-- Styled Quantity Selector with increased width -->
                                <div class="input-group quantity-selector" style="width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(-1)"><i class="bi bi-dash"></i></button>
                                    <input type="number" name="qty" id="qty" value="1" min="1" max="<?= (int)$food['stock'] ?>" class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(1)"><i class="bi bi-plus"></i></button>
                                </div>
                                <!-- ========= END OF UPDATED PART ========= -->
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-add-to-cart w-100"><i class="bi bi-basket-fill"></i> Add to Cart</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                <?php endif; ?>
                
                <div class="d-grid mt-3">
                     <a href="view_reviews.php?food_id=<?= $food['id'] ?>" class="btn btn-outline-primary btn-view-reviews"><i class="bi bi-star-fill"></i> View Reviews</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const qtyInput = document.getElementById("qty");
    const maxStock = qtyInput ? parseInt(qtyInput.getAttribute('max')) || 1 : 1;

    function updateQty(change) {
        if (!qtyInput) return;
        
        let currentQty = parseInt(qtyInput.value) || 1;
        let newQty = currentQty + change;
        
        qtyInput.value = validateQty(newQty);
    }

    function validateQty(value) {
        let numValue = parseInt(value);

        if (isNaN(numValue) || numValue < 1) {
            return 1;
        }
        
        if (numValue > maxStock) {
            return maxStock;
        }
        
        return numValue;
    }

    if (qtyInput) {
        qtyInput.addEventListener('blur', function() {
            this.value = validateQty(this.value);
        });

        qtyInput.addEventListener('input', function() {
            if (parseInt(this.value) > maxStock) {
                this.value = maxStock;
            }
             this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>