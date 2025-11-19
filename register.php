<?php
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name         = trim($_POST['name'] ?? '');
    $dob          = $_POST['dob'] ?? '';
    $gender       = $_POST['gender'] ?? '';
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['newpassword'] ?? '';

    // ------------ VALIDATION ------------

    if (!$name || !$email || !$password || !$confirm_pass || !$dob) {
        header("Location: register.php?error=All fields are required");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: register.php?error=Password must be at least 8 characters");
        exit;
    }

    if ($password !== $confirm_pass) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    // ------------ CHECK EXISTING EMAIL ------------

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=Email already registered");
        exit;
    }

    // ------------ INSERT USER ------------

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, dob, gender, email, password, created_at) 
                            VALUES (?,?,?,?,?, NOW())");

    $stmt->bind_param("sssss", $name, $dob, $gender, $email, $hashed);

    if ($stmt->execute()) {
        header("Location: login.php?success=Registration successful! Please login.");
        exit;
    } else {
        header("Location: register.php?error=Something went wrong, could not register");
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="style/register.css">
</head>
<body>

  <div class="profile-card">
    <h2 class="title">💰 Daily Expenses Tracker</h2>
    <p>Create your account</p>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error" id="msg-box">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" id="msg-box">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Notice: action points to register.php itself -->
    <form class="profile-form" method="POST" action="register.php">

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name">
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email">
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob">
      </div>

      <div class="form-group">
        <label>Gender</label>
        <select name="gender" >
          <option value="">--Select--</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="At least 8 characters" >
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="newpassword" placeholder="Confirm your password" >
      </div>

      <!-- Buttons -->
      <div class="form-actions">
        <button type="submit" class="btn-primary">Sign Up</button>
        <p class="signup">Already have an account? <a href="login.php">Sign In</a></p>
      </div>
    </form>
  </div>

</body>
</html>