<?php
require 'config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['schoolName'])) {
        throw new Exception('Invalid input data');
    }

    $schoolName = trim($data['schoolName']);
    
    if (empty($schoolName)) {
        throw new Exception('School name cannot be empty');
    }

    // Check if school exists
    $stmt = $pdo->prepare("SELECT id FROM schools WHERE school_name = ?");
    $stmt->execute([$schoolName]);
    
    if ($stmt->fetch()) {
        throw new Exception("School '$schoolName' already exists");
    }

    // Insert new school
    $stmt = $pdo->prepare("INSERT INTO schools (school_name) VALUES (?)");
    if (!$stmt->execute([$schoolName])) {
        throw new Exception("Failed to insert school");
    }

    // Get the newly inserted school data
    $newSchool = $pdo->query("SELECT id, school_name FROM schools WHERE id = " . $pdo->lastInsertId())->fetch();
    
    echo json_encode([
        'success' => true,
        'school' => $newSchool,
        'message' => 'School added successfully'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}