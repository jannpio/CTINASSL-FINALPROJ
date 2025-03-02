<?php
session_start();

// Check if the CSRF token is valid
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: login.php");
    die("CSRF validation failed.");
    
}

session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>