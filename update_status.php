<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
    $stmt->execute([$status, $studentId]);

    header('Location: ca_dashboard.php');
    exit;
}
?>