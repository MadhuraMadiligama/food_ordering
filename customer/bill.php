<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

// Get last order for this customer
$user_id = $_SESSION['user_id'];
$order_result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
if (!$order_result || $order_result->num_rows === 0) {
    echo "Order not found.";
    exit();
}
$order = $order_result->fetch_assoc();
$order_id = $order['id'];

// Get customer info (name, phone, address)
$user_result = $conn->query("SELECT name, phone, address FROM users WHERE id = $user_id");
$user = $user_result->fetch_assoc();

// Get order items
$items_result = $conn->query("
    SELECT fi.name, oi.quantity, oi.price 
    FROM order_items oi
    JOIN food_items fi ON oi.food_id = fi.id
    WHERE oi.order_id = $order_id
");

unset($_SESSION["cart"]); // Clear cart
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt - Order #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .receipt-box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="receipt-box">
        <h2 class="text-success text-center">✅ Payment Successful!</h2>
        <hr>
        <h5>🧾 Receipt - Order #<?= $order_id ?></h5>
        <p><strong>👤 Customer:</strong> <?= htmlspecialchars($user['name'] ?? 'N/A') ?></p>
        <p><strong>📞 Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
        <p><strong>📍 Address:</strong> <?= htmlspecialchars($user['address'] ?? 'N/A') ?></p>
        <p><strong>🕒 Order Date:</strong> <?= $order['order_date'] ?></p>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Food Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0; ?>
                <?php while ($item = $items_result->fetch_assoc()):
                    $item_total = $item['price'] * $item['quantity'];
                    $grand_total += $item_total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rs. <?= number_format($item['price'], 2) ?></td>
                    <td>Rs. <?= number_format($item_total, 2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                    <td><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
                </tr>
            </tbody>
        </table>

        <p><strong>💳 Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></p>
        <p><strong>📝 Notes:</strong> <?= $order['notes'] ? nl2br(htmlspecialchars($order['notes'])) : '—' ?></p>

        <div class="mt-4 text-center">
            <a href="orders.php" class="btn btn-primary">📦 View My Orders</a>
            <a href="menu.php" class="btn btn-secondary">🍔 Back to Menu</a>
        </div>
    </div>
</div>
</body>
</html>
