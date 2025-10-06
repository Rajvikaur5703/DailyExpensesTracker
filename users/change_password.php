<?php
//session_start();
include '../config/config.php';
include '../includes/session_check.php';
// Check if user is logged in


$user_id = $_SESSION['user_id'];
$msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Fetch current hashed password from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_pass, $db_password)) {
        $msg = "❌ Current password is incorrect!";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "⚠️ New passwords do not match!";
    } else {
        // Hash and update new password
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $update->bind_param("si", $hashed_pass, $user_id);

        if ($update->execute()) {
            $msg = "✅ Password changed successfully!";
        } else {
            $msg = "❌ Error updating password!";
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link rel="stylesheet" href="../style/change_password.css">
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
        <h2>🔑 Change Password</h2>

        <?php if (!empty($msg)) { echo "<p class='msg'>$msg</p>"; } ?>

        <form method="POST" class="form-box">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
</body>
</html>

