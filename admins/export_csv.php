<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=order_report.csv");

$output = fopen("php://output", "w");
fputcsv($output, ['Order ID', 'User ID', 'Total', 'Status', 'Date']);

$result = $conn->query("SELECT id, user_id, total, status, created_at FROM orders");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
