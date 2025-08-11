<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$cart = $_SESSION["cart"] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$grand_total = array_sum(array_map(fn($item) => $item["price"] * $item["quantity"], $cart));







?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>📝 Order Summary</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Food Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item["name"]) ?></td>
                <td><?= $item["quantity"] ?></td>
                <td>Rs. <?= number_format($item["price"], 2) ?></td>
                <td>Rs. <?= number_format($item["price"] * $item["quantity"], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    <a href="cart.php" class="btn btn-secondary">Edit Cart</a>
</div>
</body>
</html>
