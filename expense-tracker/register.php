<?php
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name         = trim($_POST['name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'];
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['newpassword'] ?? '';

    //  ---------------------Validation---------------
    // (i)Empty field check
    if (!$name || !$email || !$password || !$confirm_pass || !$dob ) {
        echo "<script>alert('All fields are required!');
        window.location=register.php;</script>";
        exit;
    }

    //(ii) Password length check
    if (strlen($password) < 8) {
        echo "<script>alert('Password must be more than 8 characters!');
        window.location=register.php;</script>";
        exit;
    }

    // (iii)Password match check
    if ($password !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!');
        window.location=register.php;</script>";
        exit;
    }

    // -----------Email exists check----------------
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result=$stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!');
        window.location=login.php;</script>";
        exit;
    }

    //-------------- Insert user with hashed password------------------
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("INSERT INTO users (name,dob,gender, email, password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $name,$dob,$gender,$email, $hashed);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Please login.');
            window.location= 'login.php';
        </script>";
    } else {
        echo "<script>alert('Error: Could not register.');
        window.location=register.php;</script>";
    }
}
?>


<html>
<head>
    <title>Register Page</title>
    <link href="style/register.css" rel="stylesheet"> 

</head>
<body>
    <form method="POST" action="register.php">
        <div class="register-box">
            <div class="register-title">
                <h1>💰 Expense Tracker</h1>
                <p>Create your account</p>
            </div>

            <div class="register-main">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name">

                <label>Date of Birth</label>
                <input type="date" name="dob" placeholder="Enter your date of birth">

                <label>Gender:</label>
                <div class="gender-option">
                    <label>
                        <input type="radio" name="gender" value="Male">Male
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Female">Female
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Other">Other
                    </label>
                </div>

                <label>Email</label>
                <input type="text" name="email" placeholder="Enter your email">

                <label>Password</label>
                <input type="password" name="password" placeholder="Create your password(min 8 characters)">

                <label>Confirm Password</label>
                <input type="password" name="newpassword" placeholder="Confirm your password">

                <button type="submit" class="btn-primary">Sign Up</button>

                <p class="signup">Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </form>
</body>
</html>