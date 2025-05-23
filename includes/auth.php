<?php
// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Restrict access based on role
$allowedRoles = ['main_admin', 'course_admin'];
if (!in_array($_SESSION['role'], $allowedRoles)) {
    die("Access denied.");
}

// Only include these for main_admin to prevent unnecessary DB calls for course admins
if ($_SESSION['role'] === 'main_admin') {
    require_once __DIR__.'/db.php';
    require_once __DIR__.'/functions.php';
    
    // Run cleanup if last run was >24 hours ago
    $cleanupInterval = 86400; // 24 hours in seconds
    $shouldRunCleanup = !isset($_SESSION['last_cleanup']) || 
                       (time() - $_SESSION['last_cleanup'] > $cleanupInterval);
    
    if ($shouldRunCleanup) {
        try {
            cleanupOldEnrolledStudents($pdo, 3); // 3 months retention
            $_SESSION['last_cleanup'] = time();
            
            // Optional: Store cleanup stats
            $_SESSION['last_cleanup_count'] = $pdo->query("SELECT ROW_COUNT()")->fetchColumn();
        } catch (Exception $e) {
            error_log("Automatic cleanup failed: " . $e->getMessage());
            // Continue execution even if cleanup fails
        }
    }
}
?>