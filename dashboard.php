<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-in</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/dashboard.css">
<?php
// Include session and access control
require_once 'includes/auth.php';

// Ensure only main_admin can access this page
if ($_SESSION['role'] !== 'main_admin') {
    die("Access denied.");
}

// Include the database connection and functions
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle tab selection
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'students';
$allowedTabs = ['students', 'courses', 'schools', 'reports', 'messages', 'logout'];
if (!in_array($tab, $allowedTabs)) {
    $tab = 'students'; // Default tab
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="bg/oc.png" alt="Olivarez College">
        </div>
        <div class="nav-buttons">
            <button onclick="window.location.href='?tab=students'">Manage Students</button>
            <button onclick="window.location.href='?tab=courses'">Add Course + Admin</button>
            <button onclick="window.location.href='?tab=schools'">Manage Schools</button>
            <button onclick="window.location.href='?tab=reports'">Reports</button>
            <button onclick="window.location.href='?tab=messages'">Chat/Email Blast</button>
            <button onclick="window.location.href='?tab=logout'">Logout</button>
        </div>
    </div>

    <div class="content">
        <?php
        // Include the selected tab's content
        include "includes/tabs/admin/$tab.php";
        ?>
    </div>
</body>
</html>