<?php
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name         = trim($_POST['name'] ?? '');
    $dob          = $_POST['dob'] ?? '';
    $gender       = $_POST['gender'] ?? '';
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['newpassword'] ?? '';

    //  ---------------------Validation---------------
    // (i)Empty field check
    if (!$name || !$email || !$password || !$confirm_pass || !$dob ) {
        echo "<script>alert('All fields are required!');
        window.location='register.php';</script>";
        exit;
    }

    //(ii) Password length check
    if (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters!');
        window.location='register.php';</script>";
        exit;
    }

    // (iii)Password match check
    if ($password !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!');
        window.location='register.php';</script>";
        exit;
    }

    // -----------Email exists check----------------
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result=$stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!');
        window.location='login.php';</script>";
        exit;
    }

    //-------------- Insert user with hashed password------------------
    $hashed = password_hash($password, PASSWORD_DEFAULT);

     // Insert user, now including created_at
    $stmt = $conn->prepare("INSERT INTO users (name,dob,gender,email,password,created_at) VALUES (?,?,?,?,?,NOW())");
    $stmt->bind_param("sssss", $name, $dob, $gender, $email, $hashed);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Please login.');
            window.location= 'login.php';
        </script>";
    } else {
        echo "<script>alert('Error: Could not register.');
        window.location='register.php';</script>";
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
    <h2 class="title">💰 Expense Tracker</h2>
    <p>Create your account</p>

    <!-- Notice: action points to register.php itself -->
    <form class="profile-form" method="POST" action="register.php">

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" required>
      </div>

      <div class="form-group">
        <label>Gender</label>
        <select name="gender" required>
          <option value="">--Select--</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="At least 8 characters" required>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="newpassword" placeholder="Confirm your password" required>
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
