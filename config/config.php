<?php
$servername="localhost";
$username="root";
$password="";
$database="expense_tracker";
//create connection
$conn=mysqli_connect($servername,$username,$password,$database);
//check connection
if($conn->connect_error)
{
    die("Connection failed..!".$conn->connect_error());
}

elseif(!$conn)
{
    die("Connection failed..!".mysqli_connect_error());
}
//echo "Connection successfully";
?>