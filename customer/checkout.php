<?php
// PHP කේතය කිසිදු වෙනසක් කර නොමැත.
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {
    $_SESSION['error_message'] = "Your cart is empty. Please add items before checking out.";
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $notes = $_POST["order_notes"] ?? ''; 
    $user_id = $_SESSION["user_id"];

    $grand_total = 0;
    foreach ($cart as $item) {
        $grand_total += $item["price"] * $item["quantity"];
    }

    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, name, phone, address, total, order_date, status, notes, created_at) 
                                 VALUES (?, ?, ?, ?, ?, NOW(), 'pending', ?, NOW())");
    $stmt_order->bind_param("isssds", $user_id, $name, $phone, $address, $grand_total, $notes);

    if ($stmt_order->execute()) {
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        $stmt_order_item = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock_update = $conn->prepare("UPDATE food_items SET stock = stock - ? WHERE id = ?");

        foreach ($cart as $item) {
            $food_id = $item["id"];
            $quantity = $item["quantity"];
            $price = $item["price"];

            $stmt_order_item->bind_param("iiid", $order_id, $food_id, $quantity, $price);
            $stmt_order_item->execute();

            $stmt_stock_update->bind_param("ii", $quantity, $food_id);
            if (!$stmt_stock_update->execute()) {
                error_log("Failed to update stock for food_id: " . $food_id . " in order_id: " . $order_id . " Error: " . $stmt_stock_update->error);
            }
        }
        $stmt_order_item->close();
        $stmt_stock_update->close();

        unset($_SESSION["cart"]);
        header("Location: success.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to place order. Please try again. " . $stmt_order->error;
        $stmt_order->close();
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Checkout</title>
    <!-- Google Fonts & Bootstrap (Consistent with other pages) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Styles adapted from Login/Register pages for a light theme -->
    <style>
        /* General Body Styling (from login/register page) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; /* White background */
            color: #333333; /* Dark text for contrast */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            box-sizing: border-box;
        }

        /* Container for the form (Styled like login/register container) */
        .checkout-container {
            background-color: #f8f9fa; /* Light gray background for container */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Lighter shadow */
            max-width: 600px;
            width: 100%;
            border: 1px solid #e0e0e0; /* Light border */
        }

        /* Heading Styling */
        h2 {
            color: #ffc107; /* Yellow heading */
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Form Labels and Inputs */
        .form-label {
            color: #333333; /* Dark text color */
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: #ffffff; /* White input background */
            color: #333333;
            border: 1px solid #ced4da; /* Standard light gray border */
            border-radius: 8px;
            padding: 12px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            background-color: #ffffff;
            color: #333333;
            border-color: #ffc107; /* Yellow focus border */
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .form-control::placeholder {
            color: #888;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Button Styling */
        .btn-custom {
            background-color: #ffc107;
            border: none;
            color: #1a1a1a;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-custom:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            color: #1a1a1a;
        }

        /* Alert Messages Styling (for light theme) */
        .alert {
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 20px;
            text-align: center;
            border-width: 1px;
            border-style: solid;
        }
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }
        .alert-info {
            color: #055160;
            background-color: #cff4fc;
            border-color: #b6effb;
        }
        /* Style for Bootstrap's close button in alerts to be more visible on light backgrounds */
        .btn-close {
            filter: invert(0.5);
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <!-- PHP code for displaying messages remains unchanged -->
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

    <h2 class="mb-4">🧾 Finalize Your Order</h2>
    
    <!-- The form and its inputs remain functionally the same -->
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone:</label>
            <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <textarea name="address" id="address" class="form-control" required><?= htmlspecialchars($_SESSION['user_address'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="order_notes" class="form-label">💬 Order Notes (Optional):</label>
            <textarea name="order_notes" id="order_notes" class="form-control" placeholder="e.g., No onions, extra spicy, deliver by 7 PM"></textarea>
        </div>
        <button type="submit" class="btn btn-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-wallet-fill" viewBox="0 0 16 16">
                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542c.226-.314.306-.705.306-.958a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"/>
                <path d="M16 6.5h-5.551a2.678 2.678 0 0 1-.443 1.291C9.613 8.878 8.721 9.5 8 9.5s-1.613-.622-2.006-1.709A2.679 2.679 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"/>
            </svg>
            Place Order & Pay
        </button>
    </form>
</div>

<!-- Bootstrap JS Bundle for dismissible alerts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>