<?php
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'] ?? 0;
$msg = ''; // initialize message
$msg_type = ''; // success or error

// Handle form submit
if (isset($_POST['add_expense'])) {
    $description = $_POST['title'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $category = $_POST['category'];

    if (!is_numeric($amount) || $amount <= 0) {
        $msg = "Invalid amount! Please enter a positive number.";
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, description, amount, expense_date, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $user_id, $description, $amount, $date, $category);

        if ($stmt->execute()) {
            $msg = "Expense added successfully!";
            $msg_type = "success";
            // Optional: redirect after short delay
            // header("Refresh:2; url=dashboard.php");
        } else {
            $msg = "Error: " . $stmt->error;
            $msg_type = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Expense</title>
  <link rel="stylesheet" href="../style/add_expense.css">
</head>
<body>

<div class="container">
    <div class="container-title">
        <h1>➕ Add New Expense</h1>
    </div>

    <!-- Success/Error Message -->
    <?php if (!empty($msg)) { ?>
        <p class="msg <?php echo $msg_type === 'success' ? 'msg-success' : 'msg-error'; ?>">
            <?php echo $msg; ?>
        </p>
    <?php } ?>

    <!-- Expense Form -->
    <form class="container-main" method="POST" action="">
        <label for="title">Expense Title</label>
        <input type="text" id="title" name="title" placeholder="e.g. Grocery shopping" required>

        <label for="amount">Amount (₹)</label>
        <input type="number" id="amount" name="amount" placeholder="e.g. 1200" step="0.01" required>

        <label for="date">Date</label>
        <input type="date" id="date" name="date" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">-- Select Category --</option>
            <?php
            $catResult = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
            while ($row = $catResult->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" 
                        . htmlspecialchars($row['category_name']) . 
                     "</option>";
            }
            ?>
        </select>

        <button type="submit" name="add_expense">Add Expense</button>
    </form>
</div>

</body>
</html>
