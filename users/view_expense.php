<?php
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'] ?? 0;

// ================= Get POST values =================
$category_name = $_POST['category_name'] ?? '';
$category_id   = $_POST['category_id'] ?? '';
$filter_date   = $_POST['filter_date'] ?? '';

// ================= Fetch categories for JS =================
$categoryArray = [];
$categories = $conn->query("SELECT * FROM categories");
while($cat = $categories->fetch_assoc()) {
    $categoryArray[] = ['id' => $cat['category_id'], 'name' => $cat['category_name']];
}

// ================= Prepare SQL =================
$sql = "SELECT e.amount, e.expense_date, c.category_name
        FROM expenses e
        JOIN categories c ON e.category_id = c.category_id
        WHERE e.user_id = ?";

$params = [$user_id];
$types = "i";

if(!empty($category_id)) { // exact match from hidden input
    $sql .= " AND c.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
} elseif(!empty($category_name)) { // fallback: partial match
    $sql .= " AND c.category_name LIKE ?";
    $params[] = "%$category_name%";
    $types .= "s";
}

if(!empty($filter_date)) {
    $sql .= " AND DATE(e.expense_date) = ?";
    $params[] = $filter_date;
    $types .= "s";
}

$sql .= " ORDER BY e.expense_date DESC";
// ================= Execute Query =================
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Expenses</title>
<link rel="stylesheet" href="../style/user/view_expense.css">
</head>
<body>
<div class="container">
    <h2>Expense List</h2>

    <!-- FILTER FORM -->
    <form method="POST" class="filter-box">
        <label for="categoryInput">Category:</label>
        <input type="text" name="category_name" id="categoryInput" placeholder="Type category..." value="<?= htmlspecialchars($category_name) ?>">
        <input type="hidden" name="category_id" id="categoryHidden">

        <label for="filter_date">Date:</label>
        <input type="date" name="filter_date" id="filter_date" value="<?= htmlspecialchars($filter_date) ?>">

        <button type="submit">Filter</button>
        <a href="view_expense.php" class="reset-btn" onclick="document.getElementById('categoryHidden').value='';">Reset</a>
    </form>

    <!-- EXPENSE TABLE -->
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Amount (₹)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['category_name']) ?></td>
                        <td>₹<?= number_format($row['amount'], 2) ?></td>
                        <td><?= date("d M Y", strtotime($row['expense_date'])) ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="3">No expenses found.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="btn-back">
        <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
    </div>
</div>

<script>
// ================= JS for hidden category ID =================
const categories = <?= json_encode($categoryArray) ?>;
const input = document.getElementById('categoryInput');
const hidden = document.getElementById('categoryHidden');

input.addEventListener('input', () => {
    const typed = input.value.toLowerCase().trim();
    const match = categories.find(cat => cat.name.toLowerCase() === typed);
    hidden.value = match ? match.id : '';
});
</script>
</body>
</html>
<?php $conn->close(); ?>
