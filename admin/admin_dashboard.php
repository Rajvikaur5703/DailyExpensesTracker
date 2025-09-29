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
        <nav>
            <a href="">Dashboard</a>
            <a href="manage_users.php">Users</a>
            <a href="manage_expenses.php">Expenses</a>
            <a href="category.php">Categories</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <!-- Dashboard cards -->
         <div class="cards">
            <div class="card">
                <h3>Total Users</h3>
                <p>35</p>
            </div>

            <div class="card">
                <h3>Total Expenses</h3>
                <p>₹1,25,000</p>
            </div>

            <div class="card">
                <h3>Categories</h3>
                <p>10</p>
            </div>

            <div class="card">
                <h3>Admins</h3>
                <p>2</p>
            </div>
         </div>

         <!-- Recent Acitivity Table -->
          <h2>Recent User Activities</h2>
          <table>
            <thead>
                <tr>
                    <th>Users</th>
                    <th>Actions</th>
                    <th>Details</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rajvi kaur</td>
                    <td>Added Expenses</td>
                    <td>Food - ₹500</td>
                    <td>2025-09-01</td>
                </tr>
                <tr>
                    <td>Syed Sadiya</td>
                    <td>Updated Profile</td>
                    <td>Email Changed</td>
                    <td>2025-09-01</td>
                </tr>
            </tbody>
          </table>
    </div>
</body>
</html>