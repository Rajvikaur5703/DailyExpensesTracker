<?php
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'];

// if form submitted -> update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'];
    $mobile   = $_POST['mobile'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // update with new password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, dob=?, gender=?, mobile=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $email, $dob, $gender, $mobile, $hashed, $user_id);
    } else {
        // update without password
        $sql = "UPDATE users SET name=?, email=?, dob=?, gender=?, mobile=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $dob, $gender, $mobile, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>
            alert('Profile updated successfully!');
            window.location.href='dashboard.php';
        </script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }
}

// fetch user data
$sql = "SELECT name, email, dob, gender FROM users WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="../style/profile.css">
</head>
<body>

  <div class="profile-card">
    <h2 class="title">My Profile</h2>
    <form class="profile-form" method="POST" action="profile.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="Male" <?php if ($user['gender']==='Male') echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if ($user['gender']==='Female') echo 'selected'; ?>>Female</option>
          <option value="Other" <?php if ($user['gender']==='Other') echo 'selected'; ?>>Other</option>
        </select>
      </div>
      <!-- <div class="form-group">
        <label>Mobile</label>
        <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>">
      </div> -->
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="password" placeholder="Leave blank to keep same">
      </div>

      <!-- Buttons -->
      <div class="form-actions">
        <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-save">Save</button>
      </div>
    </form>
  </div>

</body>
</html>

