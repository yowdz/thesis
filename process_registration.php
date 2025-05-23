<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php'; // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Check if form data is received
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Sanitize and trim input data
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $middleName = trim($_POST['middleName'] ?? '');
    $school = trim($_POST['school_id']);
    $lrn = trim($_POST['lrn']);
    $course1 = trim($_POST['course1']);
    $course2 = trim($_POST['course2'] ?? '');
    $course3 = trim($_POST['course3'] ?? '');
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($school) || empty($lrn) || empty($course1) || empty($email) || empty($phone)) {
        die("Validation failed: All required fields must be filled.");
    }

    // Validate LRN (Assuming it must be a 12-digit number)
    if (!preg_match('/^\d{12}$/', $lrn)) {
        die("Validation failed: Invalid LRN.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Validation failed: Invalid email.");
    }

    // Validate phone number (Assuming 10 or 11-digit format)
    if (!preg_match('/^\d{10,11}$/', $phone)) {
        die("Validation failed: Invalid phone number.");
    }

    // Validate courses
    if (
        ($course2 !== "" && $course2 !== "None" && $course1 === $course2) ||
        ($course3 !== "" && $course3 !== "None" && ($course1 === $course3 || $course2 === $course3))
    ) {
        die("Validation failed: Duplicate courses.");
    }

try {
    // Check for duplicate email or phone number
    $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    $existingStudent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingStudent) {
        // Redirect with error (do not echo or die)
        header("Location: registration.php?error=duplicate");
        exit;
    }

    // Insert new student if no duplicate is found
    $stmt = $pdo->prepare("INSERT INTO students 
        (first_name, middle_name, last_name, email, phone, school_id, lrn, course1, course2, course3) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $success = $stmt->execute([
        $firstName, $middleName, $lastName, $email, $phone, $school, $lrn, $course1, $course2, $course3
    ]);

    if ($success) {
        header("Location: index.php?success=1");
        exit;
    } else {
        header("Location: registration.php?error=failed");
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: registration.php?error=db");
    exit;
} catch (PDOException $e) {
        // Log database error for debugging
        error_log("Database error: " . $e->getMessage());
        die("Database error: " . $e->getMessage());
    }
}
?>