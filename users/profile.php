<?php
include '../config/config.php';
include '../includes/session_check.php';

$user_id = $_SESSION['user_id'];

// Fetch existing user data
$sql = "SELECT name, email, dob, gender, profile_photo FROM users WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle Remove Photo
if (isset($_POST['remove_photo'])) {
    if (!empty($user['profile_photo']) && file_exists("../uploads/".$user['profile_photo'])) {
        unlink("../uploads/".$user['profile_photo']);
    }
    $sql = "UPDATE users SET profile_photo=NULL WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Profile photo removed!'); window.location.href='profile.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error removing photo.');</script>";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['remove_photo'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'];
    $password = $_POST['password'];

    // Handle photo upload
    $photo = $user['profile_photo']; // default to existing
    if (!empty($_FILES['profile_photo']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = "profile_" . $user_id . "_" . time() . "." . pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
            $photo = $fileName;
        } else {
            echo "<script>alert('Failed to upload photo.');</script>";
        }
    }

    // Update query
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, dob=?, gender=?,  password=?, profile_photo=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissi", $name, $email, $dob, $gender,  $hashed, $photo, $user_id);
    } else {
        $sql = "UPDATE users SET name=?, email=?, dob=?, gender=?,  profile_photo=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $dob, $gender, $photo, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>

<!-- FontAwesome for default icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../style/user/profile.css">
</head>
<body>

<div class="profile-card">
  <form class="profile-form" method="POST" action="profile.php" id="profileForm" enctype="multipart/form-data">

    <div class="profile-grid">
      <!-- Photo column -->
      <div class="profile-left">
        <div class="profile-pic">
          <label for="photoInput">
            <?php if (!empty($user['profile_photo'])): ?>
              <img id="preview" src="../uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
            <?php else: ?>
              <div id="preview" class="default-icon"><i class="fas fa-user"></i></div>
            <?php endif; ?>
          </label>
          <input type="file" name="profile_photo" id="photoInput" accept="image/*" style="display:none;">

          <?php if (!empty($user['profile_photo'])): ?>
            <button type="submit" name="remove_photo" class="btn-remove-overlay" title="Remove Photo">&times;</button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Form column -->
      <div class="profile-right">
        <h2 class="title">My Profile</h2>

        <div class="form-grid">
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
          
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep same">
          </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
          <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
          <button type="submit" class="btn btn-save">Save</button>
        </div>

      </div>
    </div>

  </form>
</div>

<!-- Live preview for profile photo -->
<script>
const photoInput = document.getElementById('photoInput');
const preview = document.getElementById('preview');

photoInput.addEventListener('change', function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      if(preview.tagName === "IMG") {
        preview.src = e.target.result;
      } else {
        preview.outerHTML = '<img id="preview" src="'+e.target.result+'" alt="Profile Photo">';
      }
    }
    reader.readAsDataURL(file);
  }
});
</script>

</body>
</html>
