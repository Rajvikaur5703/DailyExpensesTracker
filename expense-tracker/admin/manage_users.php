<?php
session_start();
include '../config/config.php';

$sql="select * from users";
$result=mysqli_query($conn,$sql);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../style/manage_users.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="admin_dashboard.php" style="color:white; margin-right:15px;">Dashboard</a>
        </nav>
    </header>

    <div class="container">
        <h2>Manage Users</h2>
        <a href="#" class="add-user">+ Add New User</a>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>DOB</th>
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
