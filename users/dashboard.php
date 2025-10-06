<?php 
session_start(); 
include '../config/config.php'; 
include '../includes/session_check.php';

$user = $_SESSION['user_id']; 

/* =======================
   Handle Delete Expense
   ======================= */
if (isset($_GET['delete_expense'])) {
    $expense_id = (int) $_GET['delete_expense'];

    $stmt = $conn->prepare("DELETE FROM expenses WHERE expense_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $expense_id, $user);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Expense deleted successfully ✅";
    header("Location: dashboard.php");
    exit();
}

/* =======================
   Handle Add Expense
   ======================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $amount   = $_POST['amount']; 
    $category = $_POST['category']; 
    $date     = $_POST['expense_date']; 
    $custom_category = $_POST['custom_category'] ?? null;

    if ($amount && $category && $date) {
        // If user chose "Other", insert new category first
        if ($category === "other" && !empty($custom_category)) {
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $custom_category);
            if ($stmt->execute()) {
                $category = $stmt->insert_id; // new category ID
            } else {
                $_SESSION['error'] = "⚠️ Failed to add custom category!";
                header("Location: dashboard.php");
                exit();
            }
            $stmt->close();
        }

        // Insert expense
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, category_id, amount, expense_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $user, $category, $amount, $date);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Expense added successfully ✅";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "⚠️ Please fill all fields!";
        header("Location: dashboard.php");
        exit();
    }
}

/* =======================
   Fetch Dashboard Data
   ======================= */
$total_expenses = $conn->query("SELECT SUM(amount) AS total FROM expenses WHERE user_id='$user'")
                   ->fetch_assoc()['total'] ?? 0; 

$this_month = $conn->query("SELECT SUM(amount) AS month_total 
                             FROM expenses 
                             WHERE user_id='$user' 
                             AND MONTH(expense_date)=MONTH(CURDATE()) 
                             AND YEAR(expense_date)=YEAR(CURDATE())")
                   ->fetch_assoc()['month_total'] ?? 0; 

$today_expenses = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                                WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")
                       ->fetch_assoc()['total'] ?? 0;

$total_income = $conn->query("SELECT SUM(amount) AS total FROM income WHERE user_id='$user'")
                    ->fetch_assoc()['total'] ?? 0;

// This month’s income
// $this_month_income = $conn->query("
//     SELECT SUM(amount) AS month_total 
//     FROM income 
//     WHERE user_id='$user' 
//     AND MONTH(income_date)=MONTH(CURDATE()) 
//     AND YEAR(income_date)=YEAR(CURDATE())
// ")->fetch_assoc()['month_total'] ?? 0;
$balance = $total_income - $total_expenses;
/* =======================
   Fetch Recent Expenses
   ======================= */
$recent_expenses_query = "
    SELECT e.expense_id, e.expense_date, c.category_name, e.amount
    FROM expenses e
    JOIN categories c ON e.category_id = c.category_id
    WHERE e.user_id = '$user'
    ORDER BY e.expense_date DESC, e.expense_id DESC
    LIMIT 10
";
$recent_expenses_result = $conn->query($recent_expenses_query);
$recent_expenses = $recent_expenses_result->fetch_all(MYSQLI_ASSOC);

/* =======================
   Fetch Categories
   ======================= */
$categories_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC"); 
$categories = $categories_result->fetch_all(MYSQLI_ASSOC); 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Dashboard</title> 
    <link rel="stylesheet" href="../style/dashboard.css"> 
</head> 
<body> 
    <!-- HEADER -->
    <header class="header"> 
        <h1>Expense Dashboard</h1> 
        <div>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></div> 
    </header> 

    <!-- TOAST NOTIFICATION -->
    <?php if(isset($_SESSION['success'])): ?>
        <div id="toast" class="msg success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div id="toast" class="msg error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>


    <!-- SIDEBAR -->
    <div class="sidebar"> 
        <h2>User</h2> 
        <ul> 
            <li><a href="#">Dashboard</a></li> 
            <li><a href="add_income.php">Add Income</a></li>
            <li><a href="view_expense.php">View Expense</a></li> 
            <li><a href="reports.php">Report</a></li> 
            <li><a href="profile.php">Profile</a></li> 
            <li><a href="change_password.php">Change Password</a></li> 
            <li><a href="../logout.php">Logout</a></li> 
        </ul> 
    </div> 

    <!-- DASHBOARD -->
    <div class="dashboard-container"> 
        <!-- Summary Cards --> 
        <div class="summary-cards"> 
            <div class='card'> 
                <h3>Today</h3> 
                <p>₹<?= number_format($today_expenses, 2) ?></p> 
            </div>

            <div class='card'> 
                <h3>This Month</h3> 
                <p>₹<?= number_format($this_month, 2) ?></p> 
            </div> 

            <div class='card'> 
                <h3>Total Expenses</h3> 
                <p>₹<?= number_format($total_expenses, 2) ?></p> 
            </div> 
            <div class='card'> 
                <h3>Balance</h3> 
                <p>₹<?= number_format($balance, 2) ?></p> 
            </div>

            <div class='card'> 
                <h3>Total Income</h3> 
                <p>₹<?= number_format($total_income, 2) ?></p> 
            </div>

        </div>


        <!-- Bottom Section -->
        <div class="bottom-section">             
            
            <!-- LEFT: Add Expense Form --> 
            <div class="add-expense"> 
                <div class="card"> 
                    <h3>➕ Add Expense</h3> 
                    <form method="POST" class="add-expense-form"> 
                        <label for="amount">Amount (₹):</label> 
                        <input type="number" name="amount" id="amount" required> 

                        <label for="category">Category:</label> 
                        <select name="category" id="category" required onchange="toggleCustomCategory(this)"> 
                            <option value="">Select Category</option> 
                            <?php foreach ($categories as $cat): ?> 
                                <option value="<?= $cat['category_id'] ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option> 
                            <?php endforeach; ?> 
                            <option value="other">➕ Other (Custom)</option>
                        </select> 

                        <!-- Hidden textbox for custom category -->
                        <div id="customCategoryDiv" style="display:none; margin-top:10px;">
                            <label for="custom_category">Enter Custom Category:</label>
                            <input type="text" name="custom_category" id="custom_category">
                        </div>

                        <label for="expense_date">Date:</label> 
                        <input type="date" name="expense_date" id="expense_date" required value="<?= date('Y-m-d')?>"> 

                        <button type="submit" name="add_expense">Add Expense</button> 
                    </form> 
                </div> 
            </div> 
            
            <!-- RIGHT: Recent Expenses --> 
            <div class="recent-expenses">
                <div class="card"> 
                    <h3>🧾 Recent Expenses</h3> 
                    <?php if ($recent_expenses): ?> 
                        <div class="table-container">                       
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Amount (₹)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_expenses as $row): ?>
                                    <tr>
                                        <td><?= date("d M Y", strtotime($row['expense_date'])) ?></td>
                                        <td><?= htmlspecialchars($row['category_name']) ?></td>
                                        <td>₹<?= number_format($row['amount'], 2) ?></td>
                                        <td class="action">
                                            <a href="dashboard.php?delete_expense=<?= $row['expense_id'] ?>" 
                                               onclick="return confirm('Are you sure you want to delete this expense?');">❌</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p style="text-align:right; margin-top:10px;">
                            <a href="view_expense.php">➡ View All</a>
                        </p>
                    <?php else: ?> 
                        <p>No expenses found</p> 
                    <?php endif; ?> 
                </div> 
            </div> 
        </div>
    </div> 

    <!-- TOAST SCRIPT -->
    <script>
        window.onload = function() {
            const toast = document.getElementById("toast");
            if (toast) {
                setTimeout(() => {
                    toast.style.display = "none";
                }, 3000); // hide after 3 sec
            }
        };

        // Show/Hide custom category input
        function toggleCustomCategory(select) {
            let customDiv = document.getElementById("customCategoryDiv");
            if (select.value === "other") {
                customDiv.style.display = "block";
            } else {
                customDiv.style.display = "none";
            }
        }
    </script>
</body> 
</html>

