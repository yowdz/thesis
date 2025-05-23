<?php
require_once 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $courseId = $_POST['courseId'] ?? null;
    $userId = $_POST['userId'] ?? null;

    if (!$courseId) {
        $_SESSION['error'] = "Course ID missing.";
        header("Location: dashboard.php?tab=courses");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Delete course admin (if exists)
        if (!empty($userId)) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :userId AND role = 'course_admin'");
            $stmt->execute(['userId' => $userId]);
        }

        // Delete course
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :courseId");
        $stmt->execute(['courseId' => $courseId]);

        $pdo->commit();
        $_SESSION['success'] = "Course and admin deleted successfully.";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['error'] = "Delete failed: " . $e->getMessage();
    }

    header("Location: dashboard.php?tab=courses");
    exit;
}
?>
