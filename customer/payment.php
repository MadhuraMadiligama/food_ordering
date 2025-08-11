<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

// Get latest order
$result = $conn->query("SELECT id, total FROM orders WHERE user_id = {$_SESSION['user_id']} ORDER BY id DESC LIMIT 1");
$order = $result->fetch_assoc();

if (!$order) {
    echo "No recent order found.";
    exit();
}

$order_id = $order['id'];
$total = $order['total'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'] ?? '';

    if ($method === 'card') {
        $card_number = $_POST['card_number'];
        $expiry = $_POST['expiry'];
        $cvv = $_POST['cvv'];

        if (empty($card_number) || empty($expiry) || empty($cvv)) {
            $errors[] = "Please enter complete card details.";
        }
    }

    if (empty($errors)) {
        $conn->query("UPDATE orders SET status = 'paid', payment_method = '$method' WHERE id = $order_id");
        header("Location: payment_success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function togglePaymentFields() {
            const method = document.getElementById("payment_method").value;
            const cardFields = document.getElementById("card-fields");
            if (method === "card") {
                cardFields.style.display = "block";
            } else {
                cardFields.style.display = "none";
            }
        }
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-3">💳 Make Payment</h3>

    <div class="card shadow p-4">
        <p>Order ID: <strong>#<?= $order_id ?></strong></p>
        <p>Total Amount: <strong>Rs. <?= number_format($total, 2) ?></strong></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Select Payment Method:</label>
                <select name="payment_method" id="payment_method" onchange="togglePaymentFields()" class="form-select" required>
                    <option value="">-- Choose --</option>
                    <!--<option value="cod">Cash on Delivery</option>-->
                    <option value="card">Credit/Debit Card</option>
                    <option value="transfer">Online Bank Transfer</option>
                </select>
            </div>

            <div id="card-fields" style="display:none;">
                <div class="mb-3">
                    <label>Card Number:</label>
                    <input type="text" name="card_number" class="form-control" maxlength="16" placeholder="1234 5678 9012 3456">
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label>Expiry Date:</label>
                        <input type="text" name="expiry" class="form-control" placeholder="MM/YY">
                    </div>
                    <div class="col-md-6">
                        <label>CVV:</label>
                        <input type="text" name="cvv" class="form-control" maxlength="4" placeholder="123">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-3">✅ Confirm & Pay</button>
        </form>
    </div>
</div>
</body>
</html>
