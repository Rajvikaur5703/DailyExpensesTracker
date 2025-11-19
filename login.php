<?php
// Start the session to store user data across pages
session_start();

// Prevent browser from caching the login page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Initialize variables for error message and email value
$error = "";
$email_value = "";

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Include database connection
    include 'config/config.php';

    // Get email and password from the form
    $email = trim($_POST['email']); // remove extra spaces
    $password = $_POST['password'];
    $email_value = htmlspecialchars($email); // make email safe for redisplay

    // Prepare SQL query to fetch user details based on email
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($row = $result->fetch_assoc()) {

        // Verify the password
        if (password_verify($password, $row['password'])) {

            // Login successful: store user info in session
            $_SESSION['success'] = "Login successful! Welcome, " . $row['name'];
            session_regenerate_id(true); // regenerate session ID for security
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Insert a record of the login (optional)
            $stmt = $conn->prepare("INSERT INTO user_logins (user_id) VALUES (?)");
            $stmt->bind_param("i", $row['user_id']);
            $stmt->execute();

            // Redirect based on user role
            if ($row['role'] === 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit;
            } else {
                header("Location: users/dashboard.php");
                exit;
            }
        } else {
            // Password is incorrect
            $error = "Wrong password!";
        }
    } else {
        // Email not found in the database
        $error = "Email not found";
    }
}
?>



<!-- html code -->
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
<form method="POST" action="login.php">

    <div class="loginbox">
        <div class="login-title">
            <h1>💰 Daily Expenses Tracker</h1>
            <p >Log in to your account</p>
        </div>

        <div class="login-main">
            <?php if(!empty($error)): ?>       
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <label>Email/Mobile Number</label>
            <input type="text" name="email" placeholder="Enter your email" value="<?php echo $email_value; ?>">

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password">

            <button type="submit" >Sign in</button>
            
            <p >Don't have an account? <a href="register.php">Sign up</a></p>

        </div>
    </div>
</form>
</body>
</html>