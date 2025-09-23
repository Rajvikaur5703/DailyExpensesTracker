<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user = $_SESSION['user_id'];

/* -------- Summary -------- */
$today = $conn->query("SELECT SUM(amount) AS today_total FROM expenses WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")->fetch_assoc()['today_total'] ?? 0;

$this_week = $conn->query("SELECT SUM(amount) AS week_total FROM expenses WHERE user_id='$user' AND YEARWEEK(expense_date,1)=YEARWEEK(CURDATE(),1)")->fetch_assoc()['week_total'] ?? 0;

$this_month = $conn->query("SELECT SUM(amount) AS month_total FROM expenses WHERE user_id='$user' AND MONTH(expense_date)=MONTH(CURDATE()) AND YEAR(expense_date)=YEAR(CURDATE())")->fetch_assoc()['month_total'] ?? 0;

$highest = $conn->query("SELECT MAX(amount) AS highest FROM expenses WHERE user_id='$user'")->fetch_assoc()['highest'] ?? 0;

/* -------- Category Wise -------- */
$result_cat = $conn->query("SELECT c.category_name, SUM(e.amount) as total 
                            FROM expenses e
                            JOIN categories c ON e.category_id = c.category_id
                            WHERE e.user_id='$user'
                            GROUP BY c.category_name");

$categories = [];
$totals = [];
if ($result_cat) {
    while ($row = $result_cat->fetch_assoc()) {
        $categories[] = $row['category_name'];
        $totals[] = $row['total'];
    }
}

/* -------- Last 7 Days -------- */
$result7 = $conn->query("SELECT DATE(expense_date) as edate, SUM(amount) as total
                         FROM expenses
                         WHERE user_id='$user' 
                           AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                         GROUP BY DATE(expense_date)
                         ORDER BY edate ASC");

$days = [];
$day_totals = [];
if ($result7) {
    while ($row = $result7->fetch_assoc()) {
        $days[] = date("d M", strtotime($row['edate']));
        $day_totals[] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expense Report</title>
  <link rel="stylesheet" href="../style/reports.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
  <div class="top-bar">
    <h1>📊 Expense Report</h1>
    <button class="download-btn" onclick="downloadPDF()">⬇ Download Report</button>
  </div>


  <!-- Summary Cards -->
  <div class="summary-wrapper" id="report-content">
    <div class="summary-card">
      <h3>Today</h3>
      <p>₹<?= number_format($today, 2) ?></p>
    </div>
    <div class="summary-card">
      <h3>This Week</h3>
      <p>₹<?= number_format($this_week, 2) ?></p>
    </div>
    <div class="summary-card">
      <h3>This Month</h3>
      <p>₹<?= number_format($this_month, 2) ?></p>
    </div>
  </div>

  <div class="charts-wrapper">
    <!-- Doughnut Chart Card (Left) -->
    <div class="chart-box category">
        <h2>🥧 Category Distribution</h2>
        <canvas id="expenseCategoryChart"></canvas>
    </div>

    <!-- Bar Chart Card (Right) -->
    <div class="chart-box bar-chart">
        <h2>📈 Last 7 Days Spending</h2>
        <canvas id="expense7DaysChart"></canvas>
    </div>
</div>


  <script>
    // Doughnut Chart: Category Distribution
    const ctxCategory = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(ctxCategory, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
          data: <?= json_encode($totals) ?>,
          backgroundColor: [
            '#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#FF9F40','#00A36C','#FF6F61'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 20
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.label || '';
                let value = context.raw || 0;
                let total = context.chart._metasets[context.datasetIndex].total;
                let percentage = ((value / total) * 100).toFixed(1) + '%';
                return label + ': ₹' + value + ' (' + percentage + ')';
              }
            }
          }
        }
      }
    });

    // Bar Chart: Last 7 Days
    const ctx7Days = document.getElementById('expense7DaysChart').getContext('2d');
    new Chart(ctx7Days, {
      type: 'bar',
      data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
          label: 'Daily Expenses (₹)',
          data: <?= json_encode($day_totals) ?>,
          backgroundColor: '#36A2EB'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Download PDF
    async function downloadPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF("p", "pt", "a4");
      const content = document.getElementById('report-content').parentNode;

      await html2canvas(content, { scale: 1.2 }).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        doc.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
      });

      doc.save("Expense_Report.pdf");
    }
  </script>
</body>
</html>
