<?php
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch expenses with category name
$sql = "SELECT e.amount, e.expense_date, c.category_name
        FROM expenses e
        JOIN categories c ON e.category_id = c.category_id
        WHERE e.user_id = ?
        ORDER BY e.expense_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Expenses</title>
<link rel="stylesheet" href="../style/view_expense.css">
<script>
window.onpageshow = function(event) {
    if (event.persisted) {
      window.location.reload();
    }
};
</script>
</head>
<body>
<div class="container">
    <h2>Expense List</h2>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Amount (₹)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['category_name'])."</td>";
                    echo "<td>₹".number_format($row['amount'],2)."</td>";
                    echo "<td>".date("d M Y", strtotime($row['expense_date']))."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No expenses found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="btn-back">
        <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
