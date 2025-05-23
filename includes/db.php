<?php
$host = 'localhost';
$db = 'bsitwfbl_student_pre_enrollment';
$user = 'bsitwfbl_yowdyz';
$pass = 'AZdJAwXRwAcr8hk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
?>
