<?php
require_once 'includes/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("INSERT INTO students 
        (first_name, middle_name, last_name, email, phone, school_id, 
         lrn, course1, course2, course3, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->execute([
        $_POST['firstName'],
        $_POST['middleName'],
        $_POST['lastName'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['school_id'],
        $_POST['lrn'],
        $_POST['course1'],
        $_POST['course2'],
        $_POST['course3'],
        $_POST['status']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}