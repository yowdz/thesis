<?php
// Include database connection
require_once 'includes/db.php';

// Start session to handle success and error messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input fields
    $courseName = trim($_POST['courseName'] ?? '');
    $selectedCourse = trim($_POST['selectedCourse'] ?? '');
    $adminUsername = trim($_POST['adminUsername'] ?? '');
    $adminPassword = $_POST['adminPassword'] ?? '';

    // Validate input: Either courseName or selectedCourse must be provided
    if (empty($courseName) && empty($selectedCourse)) {
        $_SESSION['error'] = "Please enter a Course Name or select an existing one.";
        header("Location: dashboard.php?tab=courses"); // Redirect to the form page
        exit;
    }

    if (empty($adminUsername) || empty($adminPassword)) {
        $_SESSION['error'] = "Admin Username and Password are required.";
        header("Location: dashboard.php?tab=courses");
        exit;
    }

    // Secure password storage
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

    try {
        // Begin database transaction
        $pdo->beginTransaction();

        // Determine the course ID
        if (!empty($courseName)) {
            // Check if the course already exists
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_name = :courseName");
            $stmt->execute(['courseName' => $courseName]);
            $existingCourse = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingCourse) {
                throw new Exception("Course '$courseName' already exists.");
            }

            // Insert new course
            $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (:courseName)");
            $stmt->execute(['courseName' => $courseName]);
            $courseId = $pdo->lastInsertId();
        } else {
            // Use selected course
            $courseId = $selectedCourse;
        }

        // Check if the admin username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $adminUsername]);
        if ($stmt->fetch()) {
            throw new Exception("Admin username '$adminUsername' already exists.");
        }

        // Insert the admin
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, role, course_id) 
            VALUES (:username, :password, 'course_admin', :courseId)
        ");
        $stmt->execute([
            'username' => $adminUsername,
            'password' => $hashedPassword,
            'courseId' => $courseId
        ]);

        // Commit the transaction
        $pdo->commit();

        $_SESSION['success'] = "Course and Admin added successfully.";
        header("Location: dashboard.php?tab=courses");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: dashboard.php?tab=courses");
        exit;
    }
}
?>