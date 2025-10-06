<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_income'])) {
    $source = $_POST['source'];
    $amount = $_POST['income_amount'];
    $date   = $_POST['income_date'];
    $note   = $_POST['note'] ?? '';

    if ($source && $amount && $date) {
        $stmt = $conn->prepare("INSERT INTO income (user_id, source, amount, income_date, note) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isdss", $user, $source, $amount, $date, $note);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Income added successfully ✅";
        } else {
            $_SESSION['error'] = "⚠️ Failed to add income!";
        }
        $stmt->close();
        header("Location:add_income.php");
        exit();
    } else {
        $_SESSION['error'] = "⚠️ Please fill all fields!";
        header("Location:add_income.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Income</title>
<link rel="stylesheet" href="../style/add_income.css">
</head>
<body>

<div class="card">
    <h3>💰 Add Income</h3>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="msg success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="msg error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Source:</label>
        <input type="text" name="source" placeholder="Salary, Gift..." required>

        <label>Amount (₹):</label>
        <input type="number" name="income_amount" required>

        <label>Date:</label>
        <input type="date" name="income_date" value="<?= date('Y-m-d') ?>" required>

        <label>Note:</label>
        <textarea name="note" rows="2" placeholder="Optional"></textarea>

        <button type="submit" name="add_income">Add Income</button>
    </form>
</div>

<script>
window.onload = function() {
    const toast = document.querySelector('.msg');
    if(toast) setTimeout(() => { toast.style.display='none'; }, 3000);
}
</script>

</body>
</html>
