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

// Define monthly budget (example, could be from user settings)
$monthly_budget = 50000; // ₹50,000 budget
$balance_left = $monthly_budget - $this_month;
if($balance_left < 0) $balance_left = 0; // prevent negative

// Optional: color for balance card
$balance_color = ($balance_left > 0) ? '#4caf50' : '#f44336';

// Fetch categories for chart
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

// Last 7 days
$days = [];
$day_totals = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date("Y-m-d", strtotime("-$i days"));
    $days[] = $day;
    $res = $conn->query("SELECT SUM(amount) as total FROM expenses 
                         WHERE user_id='$user' AND DATE(expense_date)='$day'")
                         ->fetch_assoc()['total'] ?? 0;
    $day_totals[] = $res;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expense Report</title>
  <link rel="stylesheet" href="../style/reports.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="top-bar">
    <h1>📊 Expense Report</h1>
    <button class="download-btn" onclick="window.location.href='generate_report.php'">⬇ Download Report</button>
  </div>

  <!-- Summary Cards -->
  <div class="summary-wrapper">
    <div class="summary-card"><h3>Today</h3><p>₹<?= number_format($today, 2) ?></p></div>
    <div class="summary-card"><h3>This Week</h3><p>₹<?= number_format($this_week, 2) ?></p></div>
    <div class="summary-card"><h3>This Month</h3><p>₹<?= number_format($this_month, 2) ?></p></div>
  </div>
  <div class="charts-wrapper">
      <!-- Category Doughnut Chart -->
  <div class="chart-box">
    <h2>🥧 Expenses by Category</h2>
    <canvas id="expenseCategoryChart"></canvas>
  </div>

  <!-- 7 Days Bar Chart -->
  <div class="chart-box">
    <h2>📅 Last 7 Days Expenses</h2>
    <canvas id="expense7DaysChart"></canvas>
  </div>

  <script>
    // Doughnut Chart
    new Chart(document.getElementById('expenseCategoryChart'), {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
          data: <?= json_encode($totals) ?>,
          backgroundColor: [
            '#1976d2','#64b5f6','#90caf9','#0d47a1','#42a5f5','#1565c0','#2196f3','#1e88e5'
          ]
        }]
      },
      options: {
        responsive: true,
        cutout: '60%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle'
            }
          }
        }
      }
    });

    // 7 Days Bar Chart
    new Chart(document.getElementById('expense7DaysChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
          label: 'Daily Expenses (₹)',
          data: <?= json_encode($day_totals) ?>,
          backgroundColor: '#1976d2'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
  </div>
</body>
</html>