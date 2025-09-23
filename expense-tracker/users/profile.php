<?php
//session_start();
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'];

// if form submitted -> update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // update with new password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, role=?, dob=?, gender=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $email, $role, $dob, $gender, $hashed, $user_id);
    } else {
        // update without changing password
        $sql = "UPDATE users SET name=?, email=?, role=?, dob=?, gender=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $role, $dob, $gender, $user_id);
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
$sql = "SELECT name, user_id, email, role, dob, gender FROM users WHERE user_id=?";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/profile.css">
    <script>
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
    </script>
</head>
<body>
    <section class="profile-card">
        <h2 class="title"><i class="fa-solid fa-circle-user"></i> My Profile</h2>

        <form action="profile.php" method="POST" class="profile-form">
            <div class="form-group">
                <label><i class="fa-solid fa-id-card"></i> Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-cake-candles"></i> Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-venus-mars"></i> Gender</label>
                <select name="gender">
                    <option value="Male"   <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other"  <?php if ($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> New Password</label>
                <input type="password" name="password" placeholder="Enter new password (leave blank to keep same)">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-user-tag"></i> Role</label>
                <input type="text" name="role" value="<?php echo htmlspecialchars($user['role']); ?>">
            </div>

            <!-- Buttons -->
            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-light"><i class="fa-solid fa-xmark"></i> Cancel</a>
                <button type="submit" class="btn btn-primary" name="save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>
        </form>
    </section>
</body>
</html>
