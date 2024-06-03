<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the Home page
header("Location: Home.php");  //redirect to homepage
exit;
?>