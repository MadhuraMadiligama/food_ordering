<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_POST["order_id"]);
$user_id = $_SESSION["user_id"];

// Get current status
$query = $conn->prepare("SELECT status, payment_status FROM orders WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $order_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
    $order = $result->fetch_assoc();

    $status = $order["status"];
    $payment = $order["payment_status"];

    if ($status === 'Pending' && $payment === 'Unpaid') {
        $update = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
        $update->bind_param("i", $order_id);
        $update->execute();
    }
}

header("Location: orders.php");
exit();
