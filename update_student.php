<?php
require_once 'includes/db.php';
header('Content-Type: application/json');

try {
    $id = $_POST['studentId'];
    $stmt = $pdo->prepare("UPDATE students SET 
        first_name = ?, 
        middle_name = ?,
        last_name = ?,
        email = ?,
        phone = ?,
        school_id = ?,
        lrn = ?,
        course1 = ?,
        course2 = ?,
        course3 = ?,
        status = ?
        WHERE id = ?");

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
        $_POST['status'],
        $id
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}