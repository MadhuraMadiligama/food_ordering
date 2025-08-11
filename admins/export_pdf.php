<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Use absolute path for reliability
require_once(__DIR__ . '/../lib/tcpdf/tcpdf.php');
include("../includes/db.php");

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// PDF Content
$html = '<h2>🧾 Order Report</h2>
<table border="1" cellpadding="5">
<tr>
    <th><b>ID</b></th>
    <th><b>User ID</b></th>
    <th><b>Total (Rs)</b></th>
    <th><b>Status</b></th>
    <th><b>Date</b></th>
</tr>';

$result = $conn->query("SELECT id, user_id, total, status, created_at FROM orders");

while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>{$row['id']}</td>
        <td>{$row['user_id']}</td>
        <td>Rs. " . number_format($row['total'], 2) . "</td>
        <td>{$row['status']}</td>
        <td>{$row['created_at']}</td>
    </tr>";
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('order_report.pdf', 'D');
exit();
