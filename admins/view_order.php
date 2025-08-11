<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'] ?? 0;

// Fetch order details (include notes)
$order_query = $conn->prepare("SELECT orders.id, orders.order_date, orders.status, orders.total, orders.notes,
                                      orders.name, orders.phone, orders.address,
                                      users.name AS customer_name
                               FROM orders
                               JOIN users ON orders.user_id = users.id
                               WHERE orders.id = ?");

$order_query->bind_param("i", $order_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found!";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch order items
$items_query = $conn->prepare("SELECT fi.name, oi.quantity, oi.price
                               FROM order_items oi
                               JOIN food_items fi ON oi.food_id = fi.id
                               WHERE oi.order_id = ?");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$items_result = $items_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>📋 Order #<?= $order['id'] ?> Details</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>📍Address:</strong> <?= htmlspecialchars($order['address'] ?? 'N/A') ?></p>
    <p><strong>📞 Phone:</strong> <?= $order['phone'] ? htmlspecialchars($order['phone']) : 'N/A' ?></p>
    <p><strong>👤 Name:</strong> <?= $order['name'] ? htmlspecialchars($order['name']) : 'N/A' ?></p>
    <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>📝 Notes:</strong> <?= $order['notes'] ? nl2br(htmlspecialchars($order['notes'])) : '—' ?></p>

    <table class="table table-bordered bg-white mt-3">
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
                $total = $item['price'] * $item['quantity'];
                $grand_total += $total;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rs. <?= number_format($item['price'], 2) ?></td>
                <td>Rs. <?= number_format($total, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <a href="manage_orders.php" class="btn btn-secondary">⬅ Back to Orders</a>
</div>
</body>
</html>
