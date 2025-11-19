<?php
session_start();
include '../config/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all admins
$sql = "SELECT user_id, name, email, Gender, created_at FROM users WHERE role='admin'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management - Expense Tracker</title>
    <link rel="stylesheet" href="../style/admin/admin.css">
</head>
<body>
    <header>
        <h1>Admin Management</h1>
        <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
    </header>

    <div class="container">
        <h2>All Admins</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['user_id']."</td>";
                        echo "<td>".htmlspecialchars($row['name'])."</td>";
                        echo "<td>".htmlspecialchars($row['email'])."</td>";
                        echo "<td>".htmlspecialchars($row['Gender'])."</td>";
                        echo "<td>".$row['created_at']."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No Admins Found!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>