// dashboard-charts.js

// Initialize Category Distribution Doughnut Chart
const categoryChartCanvas = document.getElementById('categoryChart');
if (categoryChartCanvas && typeof categoryChartData !== 'undefined') {
  new Chart(categoryChartCanvas, {
    type: 'doughnut',
    data: {
      labels: categoryChartData.labels,
      datasets: [{
        data: categoryChartData.data,
        backgroundColor: ['#0ea5e9', '#16a34a', '#dc2626', '#f59e0b', '#8b5cf6']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
}

// Initialize Last 7 Days Bar Chart
const barChartCanvas = document.getElementById('barChart');
if (barChartCanvas && typeof barChartData !== 'undefined') {
  new Chart(barChartCanvas, {
    type: 'bar',
    data: {
      labels: barChartData.labels,
      datasets: [{
        label: "Expenses (₹)",
        data: barChartData.data,
        backgroundColor: '#0ea5e9'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
}
