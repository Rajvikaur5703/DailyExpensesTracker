<?php
include '../config/config.php';

// Admin details
$username = "Rajvi Kaur";
$password = password_hash("rajvi@1034", PASSWORD_BCRYPT); // secure hashing
$email = "kaurrajvi34@gamil.com";
$role = "admin";

// Insert query
$sql = "INSERT INTO users (name, password, email, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password, $email, $role);

if ($stmt->execute()) {
    echo "Admin added successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
