<?php
session_start();
include '../config/config.php';

$user_id="";
$category="";
$date="";

if($_SERVER['REQUEST_METHOD']=='POST')
{
  $user_id=$_POST['user_id'];
  $category=$_POST['category'];
  $date=$_POST['date'];
}

$sql = "SELECT e.expense_id, u.name AS user_name, c.category_name, 
               e.amount, e.expense_date
        FROM expenses e
        JOIN users u ON e.user_id = u.user_id
        JOIN categories c ON e.category_id = c.category_id
        WHERE role='user'";


if($user_id != "")
{
    $sql .=" AND e.user_id = '".mysqli_real_escape_string($conn,$user_id). "'"; //mysqli_real_escap_string= it escapes the special charecters in string before sending to my sql
}

if($category != "")
{
    $sql .=" AND c.category_name = '".mysqli_real_escape_string($conn,$category). "'";
}

if($date != "")
{
    $sql .=" AND e.expense_date = '".mysqli_real_escape_string($conn,$date). "'";
}

$sql .=" ORDER BY e.expense_date DESC";
$result= mysqli_query($conn,$sql);

$user_result = mysqli_query($conn, "SELECT user_id,name FROM users ORDER BY name");

?>



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
        <a href="admin_dashboard.php">Dashboard</a>
        <!-- <nav>
            
        </nav> -->
    </header>

    <div class="container">
        <h2>Manage Expenses</h2>
        <form action="manage_expenses.php" method="POST" class="filter-form">
          <label>User:</label>
          <select name="user_id">
            <option value="">All Users</option>
            <?php while($u=mysqli_fetch_assoc($user_result)) 
                  { ?>
                    <option value="<?php echo $u['user_id']; ?>" <?php if($user_id==$u['user_id']) echo "selected"; ?>>
                      <?php echo htmlspecialchars($u['name']); ?>
                    </option>
                    
                    <?php } ?>
          </select>


          <label>Category:</label>
          <select name="category">
            <option value="">All category</option>
            <?php
            // Fetch categories dynamically
            $catResult = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
            while ($row = $catResult->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" 
                        . htmlspecialchars($row['category_name']) . 
                     "</option>";
            }
            ?>
          
          </select>

          <label>Date:</label>
          <input type="date" name="date" value="<?php echo $date; ?>">

          <button type="submit">Filter</button>
          <button type="submit">Clear</button>
        </form>
        <table>
      <thead>
        <tr>
          <th>Expense ID</th>
          <th>User</th>
          <th>Category</th>
          <th>Amount</th>
          <th>Date</th>

        </tr>
      </thead>
      <tbody>
          <?php
            if(mysqli_num_rows($result) > 0)
            {
              while($row=mysqli_fetch_assoc($result))
              {
                echo "<tr>";
                echo "<td>".$row['expense_id']."</td>";
                echo "<td>".htmlspecialchars($row['user_name'])."</td>";
                echo "<td>".htmlspecialchars($row['category_name'])."</td>";
                echo "<td>".$row['amount']."</td>";
                echo "<td>".$row['expense_date']."</td>";
                echo "</tr>";
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
