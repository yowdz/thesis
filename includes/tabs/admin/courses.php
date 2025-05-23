<?php
require_once 'includes/db.php';
session_start();

// Fetch courses with course admin
$query = "
    SELECT c.id AS course_id, c.course_name, u.username AS admin_username, u.id AS user_id
    FROM courses c
    LEFT JOIN users u ON c.id = u.course_id AND u.role = 'course_admin'
    ORDER BY c.course_name ASC
";
$stmt = $pdo->query($query);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Courses</h2>

<!-- Flash Messages -->
<?php if (!empty($_SESSION['error'])): ?>
    <div style="color: red;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div style="color: green;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<!-- Add New Course Form -->
<h3>Add New Course</h3>
<form action="add_course.php" method="POST">
    <label for="courseName">New Course Name:</label>
    <input type="text" name="courseName" required>

    <label for="adminUsername">Admin Username:</label>
    <input type="text" name="adminUsername" required>

    <label for="adminPassword">Admin Password:</label>
    <input type="password" name="adminPassword" required>

    <button type="submit">Add Course</button>
</form>

<hr>

<!-- Edit Existing Courses -->
<h3>Edit Existing Courses</h3>
<table border="1" cellpadding="8">
    <tr>
        <th>Course Name</th>
        <th>Admin Username</th>
        <th>New Password</th>
        <th>Action</th>
    </tr>
    <?php foreach ($courses as $course): ?>
    <tr>
        <td colspan="4">
            <form action="edit_course.php" method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="courseName" value="<?= htmlspecialchars($course['course_name']) ?>" required>
                <input type="text" name="adminUsername" value="<?= htmlspecialchars($course['admin_username']) ?>" required>
                <input type="password" name="adminPassword" placeholder="New password (optional)">
                <input type="hidden" name="courseId" value="<?= $course['course_id'] ?>">
                <input type="hidden" name="userId" value="<?= $course['user_id'] ?>">
                <button type="submit">Save</button>
            </form>

            <!-- Delete Form -->
            <form action="delete_course.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this course and its admin?');" style="display: inline;">
                <input type="hidden" name="courseId" value="<?= $course['course_id'] ?>">
                <input type="hidden" name="userId" value="<?= $course['user_id'] ?>">
                <button type="submit" style="background-color: red; color: white;">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
