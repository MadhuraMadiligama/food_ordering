<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Date Filter
$start = $_GET['start'] ?? date('Y-m-d', strtotime('-7 days'));
$end = $_GET['end'] ?? date('Y-m-d');

// Total Orders
$total_orders_result = $conn->query("SELECT COUNT(*) AS total FROM orders");
$total_orders = $total_orders_result->fetch_assoc()['total'];

// Total Revenue (Completed orders)
$revenue_result = $conn->query("SELECT SUM(total) AS revenue FROM orders WHERE status = 'Completed'");
$revenue = $revenue_result->fetch_assoc()['revenue'] ?? 0;

// Orders by Status
$status_result = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
}

// Orders by Date (filtered)
$stmt = $conn->prepare("SELECT DATE(order_date) as date, COUNT(*) as count, SUM(total) as total 
                        FROM orders WHERE order_date BETWEEN ? AND ? 
                        GROUP BY DATE(order_date) ORDER BY date ASC");
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$date_result = $stmt->get_result();

$chart_dates = [];
$chart_counts = [];
$chart_totals = [];
while ($row = $date_result->fetch_assoc()) {
    $chart_dates[] = $row['date'];
    $chart_counts[] = $row['count'];
    $chart_totals[] = $row['total'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>📊 Admin Report Dashboard</h2>

    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>

    <!-- Filters -->
    <form method="get" class="row g-2 my-4">
        <div class="col-auto">
            <label>From:</label>
            <input type="date" name="start" value="<?= $start ?>" class="form-control">
        </div>
        <div class="col-auto">
            <label>To:</label>
            <input type="date" name="end" value="<?= $end ?>" class="form-control">
        </div>
        <div class="col-auto mt-4">
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Orders</h5>
                    <h3><?= $total_orders ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Revenue</h5>
                    <h3>Rs. <?= number_format($revenue, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>



    <!-- Orders by Status -->
    <h4>🗂 Orders by Status</h4>
    <ul class="list-group mb-4">
        <?php foreach ($status_data as $status => $count): ?>
            <li class="list-group-item d-flex justify-content-between">
                <?= ucfirst($status) ?>
                <span class="badge bg-secondary"><?= $count ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="lineChart"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

<a href="order_analytics.php" class="btn btn-secondary">⬅ Order Analytics</a>
        <a href="orders_report.php" class="btn btn-secondary">⬅ Order Report</a>


    <!-- Orders Table -->
    <h4>📅 Orders in Selected Range</h4>
    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Orders</th>
                <th>Total (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($chart_dates as $i => $d): ?>
                <tr>
                    <td><?= $d ?></td>
                    <td><?= $chart_counts[$i] ?></td>
                    <td><?= number_format($chart_totals[$i], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
</div>

<!-- Chart.js Scripts -->
<script>
const lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_dates) ?>,
        datasets: [{
            label: 'Orders per Day',
            data: <?= json_encode($chart_counts) ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

const pieChart = new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($status_data)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($status_data)) ?>,
            backgroundColor: ['#ffc107', '#28a745', '#dc3545', '#17a2b8', '#6f42c1']
        }]
    }
});
</script>
</body>
</html>
