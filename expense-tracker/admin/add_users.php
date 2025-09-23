<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Daily Expense Tracker</title>
    <link rel="stylesheet" href="../style/add_user.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Add User (Admin)</h2>
            <p class="sub">Create a new user account</p>

            <form action="add_user.php" method="POST">
                <label class="full"> Name:</label>
                <input type="text" class="full" name="name">

                <label>Date of Birth:</label>
                <input type="date" name="dob">

                <label class="full">Gender:</label>
                <div class="inline-row">
                    <label><input type="radio" name="gender" value="Male">Male</label>
                    <label><input type="radio" name="gender" value="Female">Female</label>
                    <label><input type="radio" name="gender" value="Other">Other</label>
                </div>

                <label>Email:</label>
                <input type="email" name="email">

                <label>Password:</label>
                <input type="password" name="password">

                <div class="form-actions">
                    <input class="btn" type="submit" value="Add User">
                    <a href="../home.php" class="back-link">Back to Home</a>
                </div>

            </form>
        </div>
    </div>
</body>
</html>