<?php
include '../config/config.php';

// Admin details
$username = "Rajvi Kaur";
$dob="2004-03-10";
$gender="Female";
$password = password_hash("rajvi@1034", PASSWORD_BCRYPT); // secure hashing
$email = "kaurrajvi34@gamil.com";
$role = "admin";

// Insert query
$sql = "INSERT INTO users (name, DOB,Gender,password, email, role) VALUES (?, ?,?,?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $username, $dob,$gender,$password, $email, $role);

if ($stmt->execute()) {
    echo "Admin added successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>

