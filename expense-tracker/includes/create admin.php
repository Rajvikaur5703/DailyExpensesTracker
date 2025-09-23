<?php
include '../config/config.php';

$name = 'Syed Sadiya';
$DOB='2005-11-17';
$Gender='Female';
$email = 'syedsadiya@gmail.com';
$password = 'anam'; // plain text password
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute insert statement
$stmt = $conn->prepare("INSERT INTO users (name,DOB,Gender, email, password, role) VALUES (?,?,?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name,$DOB,$Gender, $email, $hashed_password, $role);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin user created successfully.";
} else {
    echo "Error creating admin user.";
}
?>
