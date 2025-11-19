<?php
// Start session if needed
session_start();
include '../config/config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Add Category
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        if ($stmt->execute()) {
            $msg = "Category added successfully!";
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Category name cannot be empty.";
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $category_id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE category_id=$category_id");
    header("Location: category.php");
    exit();
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY category_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <link rel="stylesheet" href="../style/admin/category.css"> <!-- link external css -->
</head>
<body>

<div class="category-container">
    <h2>📂 Manage Categories</h2>
    
    <!-- Show message -->
    <?php if (isset($msg)) { echo "<p class='msg'>$msg</p>"; } ?>

    <!-- Add Category Form -->
    <form method="POST" action="">
        <input type="text" name="category_name" placeholder="Enter new category" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>

    <!-- Categories Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['category_id']; ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td>
                    <a href="category.php?delete=<?php echo $row['category_id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Back to Dashboard Link -->
    <a href="admin_dashboard.php" class="btn btn-secondary back-btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>
