<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user = $_SESSION['user_id'];

// Fetch summary amounts
$today = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                       WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")
                       ->fetch_assoc()['total'] ?? 0;

$this_week = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                           WHERE user_id='$user' AND YEARWEEK(expense_date,1)=YEARWEEK(CURDATE(),1)")
                           ->fetch_assoc()['total'] ?? 0;

$this_month = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                            WHERE user_id='$user' AND MONTH(expense_date)=MONTH(CURDATE()) 
                            AND YEAR(expense_date)=YEAR(CURDATE())")
                            ->fetch_assoc()['total'] ?? 0;

// Monthly budget
$monthly_budget = 50000; // ₹50,000
$balance_left = $monthly_budget - $this_month;
if($balance_left < 0) $balance_left = 0;
$balance_color = ($balance_left > 0) ? '#4caf50' : '#f44336';

// Fetch categories for summary
$cat_query = $conn->query("SELECT c.category_name, SUM(e.amount) as total
                           FROM expenses e
                           JOIN categories c ON e.category_id = c.category_id
                           WHERE e.user_id='$user'
                           GROUP BY c.category_name");

$categories = [];
$totals = [];
while ($row = $cat_query->fetch_assoc()) {
    $categories[] = $row['category_name'];
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expense Report</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="stylesheet" href="../style/reports.css">
</head>
<body>

  <div class="top-bar">
    <h1>Expense Report</h1>
    <button class="download-btn" onclick="window.location.href='generate_report.php'">⬇ Download Report</button>
  </div>

  <!-- Summary Cards -->
  <div class="summary-wrapper">
    <div class="summary-card"><h3>Today</h3><p>₹<?= number_format($today, 2) ?></p></div>
    <div class="summary-card"><h3>This Week</h3><p>₹<?= number_format($this_week, 2) ?></p></div>
    <div class="summary-card"><h3>This Month</h3><p>₹<?= number_format($this_month, 2) ?></p></div>
    <div class="balance-card" style="background-color: <?= $balance_color ?>;">
      <h3>Balance Left</h3>
      <p>₹<?= number_format($balance_left, 2) ?></p>
    </div>
  </div>

  <div class="category-chart-wrapper">
  <!-- Left: Total Spent by Category -->
  <div class="category-summary">
    <h2>Total Spent by Category</h2>
    <ul>
      <?php foreach($categories as $i => $cat): ?>
        <li>
          <span><?= htmlspecialchars($cat) ?></span>
          <span>₹<?= number_format($totals[$i], 2) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Right: Doughnut Chart -->
  <div class="chart-box">
    <h2>Expenses Distribution</h2>
    <canvas id="expenseCategoryChart"></canvas>
  </div>
</div>

<script>
new Chart(document.getElementById('expenseCategoryChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($categories) ?>,
    datasets: [{
      data: <?= json_encode($totals) ?>,
      backgroundColor: [
        '#0078D7', '#00A2FF', '#69C9FF', '#A5E3FF', '#C7F0FF', '#E6F8FF'
      ],
      borderColor: '#fff',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '60%',
    plugins: {
      legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle' } }
    }
  }
});
</script>
</body>
</html>
