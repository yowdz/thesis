<link rel="stylesheet" href="css/reports.css">
<?php
// Ensure session and database setup
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

// Fetch data for reports
$topSchools = fetchTopSchools($pdo);
$courseReports = fetchCourseReports($pdo);
$statusReports = fetchStatusReports($pdo);
$totalStudents = fetchTotalStudents($pdo);

?>

<h1>Reports</h1>

<!-- Total student overview -->
<h2>Overall Student Enrollment</h2>
<p><strong>Total Registered Students:</strong> <?= htmlspecialchars($totalStudents['total']) ?></p>

<!-- Top Schools Report -->
<h2>Top Schools</h2>
<?php if (!empty($topSchools)): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>School Name</th>
            <th>Number of Students</th>
        </tr>
        <?php foreach ($topSchools as $school): ?>
            <tr>
                <td><?= htmlspecialchars($school['school_name']) ?></td>
                <td><?= htmlspecialchars($school['student_count']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No school data available.</p>
<?php endif; ?>

<!-- Course Reports -->
<h2>Course Reports</h2>
<?php if (!empty($courseReports)): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Course Name</th>
            <th>Students Interested</th>
            <th>Students Enrolled</th>
            <th>Total Students</th>
        </tr>
        <?php foreach ($courseReports as $course): ?>
            <tr>
                <td><?= htmlspecialchars($course['course_name']) ?></td>
                <td><?= htmlspecialchars($course['interested_count']) ?></td>
                <td><?= htmlspecialchars($course['enrolled_count']) ?></td>
                <td><?= htmlspecialchars($course['student_count']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No course data available.</p>
<?php endif; ?>

<!-- Status breakdown report -->
<h2>Student Status Breakdown</h2>
<?php if (!empty($statusReports)): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Status</th>
            <th>Number of Students</th>
        </tr>
        <?php foreach ($statusReports as $status): ?>
            <tr>
                <td><?= htmlspecialchars($status['status']) ?></td>
                <td><?= htmlspecialchars($status['count']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No status data available.</p>
<?php endif; ?>
