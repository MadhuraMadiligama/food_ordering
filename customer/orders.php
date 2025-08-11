<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$user_id = $_SESSION["user_id"];

// Get all orders
$order_query = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

$orders = [];
while ($order = $order_result->fetch_assoc()) {
    $order_id = $order["id"];

    // Get order items
    $item_query = $conn->prepare("SELECT oi.quantity, f.name, f.price 
        FROM order_items oi 
        JOIN food_items f ON oi.food_id = f.id 
        WHERE oi.order_id = ?");
    $item_query->bind_param("i", $order_id);
    $item_query->execute();
    $item_result = $item_query->get_result();

    $items = [];
    $total = 0;
    while ($item = $item_result->fetch_assoc()) {
        $item_total = $item["price"] * $item["quantity"];
        $total += $item_total;
        $items[] = [
            "name" => $item["name"],
            "quantity" => $item["quantity"],
            "price" => $item["price"],
            "total" => $item_total
        ];
    }

    $orders[] = [
        "id" => $order["id"],
        "status" => $order["status"],
        "payment_status" => $order["payment_status"], // ✅ Added here
        "items" => $items,
        "total" => $total
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">🧾 My Orders</h2>

    <?php if (count($orders) === 0): ?>
        <div class="alert alert-info">You have no orders yet.</div>
    <?php endif; ?>

    <?php foreach ($orders as $order): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Order #<?= $order["id"] ?> | 
                    Status: <span class="badge bg-<?= ($order["status"] === 'Cancelled') ? 'danger' : (($order["status"] === 'Completed') ? 'success' : 'warning') ?>">
                        <?= $order["status"] ?>
                    </span>
                    | Total: Rs. <?= number_format($order["total"], 2) ?>
                </h5>

                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($order["items"] as $item): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($item["name"]) ?> x <?= $item["quantity"] ?> - Rs. <?= number_format($item["total"], 2) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

<?php if ($order["status"] === 'Pending' && $order["payment_status"] === 'Unpaid'): ?>
    <form method="POST" action="cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
        <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
        <button type="submit" name="cancel" class="btn btn-danger btn-sm">❌ Cancel Order</button>
    </form>
<?php else: ?>
    <span class="text-muted">🔒 Not Cancelable</span>
<?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
