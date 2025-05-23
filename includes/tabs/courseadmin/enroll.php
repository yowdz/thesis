<?php
session_start();
require '../../../config.php';

// Authentication and authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'course_admin') {
    header('Location: ../../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Student ID required");
}

$student_id = $_GET['id'];

// Verify student belongs to course admin's course
$stmt = $pdo->prepare("SELECT s.id 
                      FROM students s
                      JOIN users u ON u.id = ?
                      JOIN courses c ON c.id = u.course_id
                      WHERE (s.course1 = c.course_name OR 
                             s.course2 = c.course_name OR 
                             s.course3 = c.course_name)
                      AND s.id = ?");
$stmt->execute([$_SESSION['user_id'], $student_id]);

if (!$stmt->fetch()) {
    die("Unauthorized action or student not found");
}

// Update status to Enrolled
$stmt = $pdo->prepare("UPDATE students SET status = 'Enrolled' WHERE id = ?");
$stmt->execute([$student_id]);

header("Location: ../../../ca_dashboard.php");
exit;
?>