<?php
session_start(); // Start the session to access session variables

// ------------------ Destroy all session data ------------------
// Clear all session variables
$_SESSION = [];
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session completely

// ------------------ Clear the session cookie ------------------
// Check if session cookies are being used
if (ini_get("session.use_cookies")) {
    // Get current cookie parameters
    $params = session_get_cookie_params();
    // Set the session cookie to expire in the past to remove it from the browser
    setcookie(session_name(), '', time() - 3600,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ------------------ Redirect to login page ------------------
// After logout, send user to login page
header("Location: login.php");
exit(); // Stop further execution
?>
