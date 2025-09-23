<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Expenses - Admin</title>
    <link rel="stylesheet" href="../style/manage_expenses.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_expenses.php">Manage Expense</a>
        </nav>
    </header>

    <div class="container">
        <h2>Manage Expenses</h2>
        <form action="manage_expenses.php" method="GET" class="filter-form">
          <label>User:</label>
          <select name="user_id">
            <option value="">All Users</option>
          </select>

          <label>Category:</label>
          <select name="category">
            <option value="">All category</option>
            <option value="Food">Food</option>
            <option value="Travel">Travel</option>
            <option value="Shopping">Shopping</option>
          </select>

          <label>Date:</label>
          <input type="date" name="date">

          <button type="submit">Filter</button>
        </form>
        <!-- <a href="#" class="add-expense">+Add New Expense</a> -->
        <table>
      <thead>
        <tr>
          <th>Expense ID</th>
          <th>User</th>
          <th>Category</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>E001</td>
          <td>Rajvi Kaur</td>
          <td>Food</td>
          <td>₹500</td>
          <td>2025-09-01</td>
          <td class="actions">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
        <tr>
          <td>E002</td>
          <td>Syed Sadiya</td>
          <td>Travel</td>
          <td>₹1200</td>
          <td>2025-09-02</td>
          <td class="actions">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    </div>
</body>
</html>