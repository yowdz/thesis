<link rel="stylesheet" href="css/ca.css">
<?php
session_start();
require 'config.php';

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'course_admin') {
    header('Location: login.php');
    exit;
}

// Get course admin's assigned course
$stmt = $pdo->prepare("SELECT c.course_name 
                      FROM users u
                      JOIN courses c ON u.course_id = c.id
                      WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$course = $stmt->fetch();

if (!$course) {
    die("Course assignment not found!");
}

$course_name = $course['course_name'];

// Fetch students for this specific course
$stmt = $pdo->prepare("SELECT * FROM students 
                      WHERE (course1 = ? OR course2 = ? OR course3 = ?)
                      ORDER BY status, last_name");
$stmt->execute([$course_name, $course_name, $course_name]);
$students = $stmt->fetchAll();

// Separate into interested and enrolled
$interested_students = array_filter($students, fn($s) => $s['status'] === 'Interested');
$enrolled_students = array_filter($students, fn($s) => $s['status'] === 'Enrolled');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Admin Dashboard</title>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        .tab-buttons { margin: 20px 0; }
        .action-links a { margin: 0 5px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .logout-btn {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= htmlspecialchars($course_name) ?> Admin Dashboard</h1>
        <a href="includes/tabs/courseadmin/logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="tab-buttons">
        <button onclick="showTab('interested')">Interested Students (<?= count($interested_students) ?>)</button>
        <button onclick="showTab('enrolled')">Enrolled Students (<?= count($enrolled_students) ?>)</button>
    </div>

    <!-- Interested Students Tab -->
    <div id="interested" class="tab-content active">
        <h2>Interested Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>School</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interested_students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['phone']) ?></td>
                    <td><?= htmlspecialchars($student['school_id']) ?></td>
                    <td class="action-links">
                        <a href="includes/tabs/courseadmin/enroll.php?id=<?= $student['id'] ?>" 
                           onclick="return confirm('Enroll this student?')">Enroll</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Enrolled Students Tab -->
    <div id="enrolled" class="tab-content">
        <h2>Enrolled Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>School</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrolled_students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['phone']) ?></td>
                    <td><?= htmlspecialchars($student['school_id']) ?></td>
                    <td class="action-links">
                        <a href="includes/tabs/courseadmin/unenroll.php?id=<?= $student['id'] ?>" 
                           onclick="return confirm('Unenroll this student?')">Unenroll</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>
</html>