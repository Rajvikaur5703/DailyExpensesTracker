<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user = $_SESSION['user_id'];

/* =========================
   Fetch Summary Amounts
   ========================= */
$today = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                       WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")
                       ->fetch_assoc()['total'] ?? 0;

$this_week = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                           WHERE user_id='$user' AND YEARWEEK(expense_date,1)=YEARWEEK(CURDATE(),1)")
                           ->fetch_assoc()['total'] ?? 0;

/* =========================
   Monthly & Overall Totals
   ========================= */
$this_month_expenses = $conn->query("SELECT SUM(amount) AS total 
                                    FROM expenses 
                                    WHERE user_id='$user' 
                                    AND MONTH(expense_date)=MONTH(CURDATE()) 
                                    AND YEAR(expense_date)=YEAR(CURDATE())")
                                    ->fetch_assoc()['total'] ?? 0;

$this_month_income = $conn->query("SELECT SUM(amount) AS total 
                                  FROM income 
                                  WHERE user_id='$user' 
                                  AND MONTH(income_date)=MONTH(CURDATE()) 
                                  AND YEAR(income_date)=YEAR(CURDATE())")
                                  ->fetch_assoc()['total'] ?? 0;

// Overall totals
/* =========================
   Calculate Balances
   ========================= */
$monthly_balance = $this_month_income - $this_month_expenses;


if ($monthly_balance < 0) $monthly_balance = 0;


/* =========================
   Fetch Categories for Chart
   ========================= */
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
  <link rel="stylesheet" href="../style/user/reports.css">
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
    <div class="summary-card"><h3>This Month</h3><p>₹<?= number_format($this_month_expenses, 2) ?></p></div>

    <!-- 🧾 No background colors now -->
    <div class="summary-card">
      <h3>This Month’s Balance</h3>
      <p>₹<?= number_format($monthly_balance, 2) ?></p>
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
      borderColor: '#0b0a0aff',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '60',
    plugins: {
      legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle' } }
    }
  }
});
</script>
</body>
</html>
