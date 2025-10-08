<?php
session_start();
include '../config/config.php';

// Prevent back button after logout / session expire
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sql = "
    SELECT u.name, u.email, u.Gender, u.created_at
    FROM users u
    JOIN user_logins ul ON u.user_id = ul.user_id
    WHERE DATE(ul.login_time) = CURDATE()
    AND u.role = 'user'
";
$result = mysqli_query($conn, $sql);
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Expense Tracker</title>
    <link rel="stylesheet" href="../style/admin.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <h4><div>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!
                <a href="../logout.php"><button class="btnlogout">Logout</button></a>
            </div>
        </h4>
    </header>

    <div class="container">
        <!-- Dashboard cards -->
         <div class="cards">
            <a href="manage_users.php">
            <div class="card">
                <h3>Total Users</h3>
                <!-- <p>35</p> -->
            </div></a>

            <a href="manage_expenses.php">
            <div class="card">
                <h3>Manage Expenses</h3>
                <!-- <p>₹1,25,000</p> -->
            </div></a>

            <a href="category.php">
            <div class="card">
                <h3>Categories</h3>
                <!-- <p>10</p> -->
            </div></a>
            <a href="admin_profile.php">
            <div class="card">
                <h3>Admins</h3>
                <!-- <p>2</p> -->
            </div></a>
         </div>

         <!-- Recent Acitivity Table -->
          <h2>Recent User Activities</h2>
          <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Date</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <!-- <button type="submit">View Report</button> -->
            <?php
                if ($result->num_rows > 0) 
                {
                    while ($row = $result->fetch_assoc()) 
                    {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td><form method='POST' action='generate_report.php'>
                                    <button type='submit' class='btnreport' name='generate_report'>View Report</button>
                                  </form></td>";
                        echo "</tr>";
                    }
                } 
                else
                {
                    echo "<tr><td colspan='4'>No users logged in today.</td></tr>";
                }
                ?>
            </tbody>
          </table>
    </div>
</body>
</html>