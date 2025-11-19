<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user = $_SESSION['user_id'];

/* Handle Add / Update Income */
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_income'])) {
    $source = $_POST['source'];
    $amount = $_POST['income_amount'];
    $date   = $_POST['income_date'];
    $id     = $_POST['income_id'] ?? null;

    if ($source && $amount && $date) {
        if ($id) {
            // Update income
            $stmt = $conn->prepare("UPDATE income SET source=?, amount=?, income_date=? WHERE income_id=? AND user_id=?");
            $stmt->bind_param("sdssi", $source, $amount, $date, $id, $user);
            $action = "updated";
        } else {
            // Add income
            $stmt = $conn->prepare("INSERT INTO income (user_id, source, amount, income_date) VALUES (?,?,?,?)");
            $stmt->bind_param("isds", $user, $source, $amount, $date);
            $action = "added";
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Income $action successfully ✅";
            header("Location:dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "⚠️ Failed to $action income!";
        }
        $stmt->close();
        header("Location: add_income.php");
        exit();
    } else {
        $_SESSION['error'] = "⚠️ Please fill all fields!";
        header("Location: add_income.php");
        exit();
    }
}

/* Handle Delete Income */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM income WHERE income_id=? AND user_id=?");
    $stmt->bind_param("ii", $delete_id, $user);
    if($stmt->execute()) {
        $_SESSION['success'] = "Income deleted successfully ✅";
    } else {
        $_SESSION['error'] = "⚠️ Failed to delete income!";
    }
    $stmt->close();
    header("Location: add_income.php");
    exit();
}

/* Fetch income entries */
$result = $conn->query("SELECT * FROM income WHERE user_id = $user ORDER BY income_date DESC");

/* Check if editing an income */
$edit_income = null;
if(isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM income WHERE income_id=? AND user_id=?");
    $stmt->bind_param("ii", $edit_id, $user);
    $stmt->execute();
    $edit_income = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Spendify - Manage Income</title>
<link rel="stylesheet" href="../style/user/add_income.css">
</head>
<body>

<div class="card">
    <h3>💰 <?= $edit_income ? "Edit Income" : "Add Income" ?></h3>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="msg success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="msg error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="income_id" value="<?= $edit_income['income_id'] ?? '' ?>">

        <label>Source:</label>
        <input type="text" name="source" placeholder="Salary, Gift..." value="<?= htmlspecialchars($edit_income['source'] ?? '') ?>" required>

        <label>Amount (₹):</label>
        <input type="number" name="income_amount" value="<?= htmlspecialchars($edit_income['amount'] ?? '') ?>" required>

        <label>Date:</label>
        <input type="date" name="income_date" value="<?= $edit_income['income_date'] ?? date('Y-m-d') ?>" required>

        <button type="submit" name="add_income"><?= $edit_income ? "Update Income" : "Add Income" ?></button>
    </form>

    <?php if($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Source</th>
                <th>Amount (₹)</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['source']) ?></td>
                    <td><?= htmlspecialchars($row['amount']) ?></td>
                    <td><?= htmlspecialchars($row['income_date']) ?></td>
                    <td>
                        <a class="action-btn" href="add_income.php?edit=<?= $row['income_id'] ?>">Edit</a>
                        <a class="delete-btn" href="add_income.php?delete=<?= $row['income_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No income records found.</p>
    <?php endif; ?>
</div>

<script>
window.onload = function() {
    const toast = document.querySelector('.msg');
    if(toast) setTimeout(() => { toast.style.display='none'; }, 3000);
}
</script>

</body>
</html>
