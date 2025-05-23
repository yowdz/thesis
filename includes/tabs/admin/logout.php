<?php
// Ensure session only starts if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session properly
session_destroy();

// Redirect to an absolute URL that adapts to the server
$baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
           . "://$_SERVER[HTTP_HOST]/login.php";

header("Location: $baseURL");
exit();
?>
