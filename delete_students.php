<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Check if user is main_admin
if ($_SESSION['role'] !== 'main_admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['ids']) || empty($data['ids'])) {
        throw new Exception('No students selected for deletion');
    }

    // Prepare the placeholders for the SQL query
    $placeholders = str_repeat('?,', count($data['ids']) - 1) . '?';
    
    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
    $stmt->execute($data['ids']);

    // Check if any rows were affected
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Students deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No students were deleted']);
    }

} catch (Exception $e) {
    error_log("Error deleting students: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error deleting students: ' . $e->getMessage()]);
}