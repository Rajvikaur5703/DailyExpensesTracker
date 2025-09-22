<?php
session_start();
session_unset(); //removes all session variables
session_destroy();//destroy session completely

// Prevent back after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: home.php");
exit();
?>
