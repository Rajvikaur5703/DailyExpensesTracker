<?php
session_start();
// Prevent browser from caching login page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

//if already logged in,redirect to dashboard
if(isset($_SESSION['user_id']))
{
    header("Location: dashboard.php");
    exit();
}

//handle login from submit




$error="";
$email_value="";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'config/config.php';
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $email_value=htmlspecialchars($email); //keep value safe for re-display

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // login success
            $_SESSION['success']="Login successful ! Welcome, ".$row['name'];
            session_regenerate_id(true); // regenerate session ID
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name']   = $row['name'];
            $_SESSION['role']   = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit;
            } else {
                header("Location: users/dashboard.php");
                exit;
            }
        } else {
            $error="Wrong password!";
        }
    } else {
        $error="Email not found";
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
            <h1>💰 Expense Tracker</h1>
            <p >Log in to your account</p>
        </div>

        <div class="login-main">
<!-- 
            <input list="roles" name="role" placeholder="Select Role">
            <datalist id="roles">
                <option value="User">
                <option value="Admin">
            </datalist> -->
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
