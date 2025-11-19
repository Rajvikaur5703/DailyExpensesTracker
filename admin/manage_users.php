<?php
session_start();
include '../config/config.php';

$sql="select * from users where role='user'";
$result=mysqli_query($conn,$sql);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../style/admin/manage_users.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="admin_dashboard.php" style="color:white; margin-right:15px;">Dashboard</a>
        </nav>
    </header>

    <div class="container">
        <h2>Total Users</h2>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($result->num_rows > 0)
                {
                    while($row = $result->fetch_assoc())
                    {
                        echo"<tr>";
                        echo"<td>".$row['name']."</td>";
                        echo"<td>".$row['email']."</td>";
                        echo"<td>".$row['Gender']."</td>";
                        echo"<td>".$row['DOB']."</td>";
                        echo "<td>
                                <form method='POST' action='generate_report.php' target='_blank'>
                                    <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                    <button type='submit' class='btnreport' name='generate_report'>View Report</button>
                                </form></td>";
                        echo"</tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='5'>No expenses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>