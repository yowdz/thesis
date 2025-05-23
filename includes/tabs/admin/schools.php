<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

// Handle Add School
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addSchool'])) {
    $schoolName = trim($_POST['schoolName']);

    if (!empty($schoolName)) {
        try {
            addSchool($pdo, $schoolName);
            $_SESSION['success'] = "School added successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "School name cannot be empty.";
    }

    header("Location: dashboard.php?tab=schools");
    exit;
}

// Handle Edit School
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editSchool'])) {
    $schoolId = $_POST['schoolId'];
    $newSchoolName = trim($_POST['newSchoolName']);

    if (!empty($schoolId) && !empty($newSchoolName)) {
        try {
            editSchool($pdo, $schoolId, $newSchoolName);
            $_SESSION['success'] = "School name updated successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid school ID or empty name.";
    }

    header("Location: dashboard.php?tab=schools");
    exit;
}

// Handle Delete School
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteSchool'])) {
    $schoolId = $_POST['schoolId'];

    if (!empty($schoolId)) {
        try {
            deleteSchool($pdo, $schoolId);
            $_SESSION['success'] = "School deleted successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid school ID.";
    }

    header("Location: dashboard.php?tab=schools");
    exit;
}

// Fetch updated schools data
$schools = fetchSchools($pdo);
?>
<link rel="stylesheet" href="css/school.css">
<h1>Manage Schools</h1>

<!-- Display Error/Success Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h2>Add School</h2>
<form method="POST">
    <input type="text" name="schoolName" placeholder="Enter new school name" required>
    <button type="submit" name="addSchool">Add</button>
</form>

<h2>Existing Schools</h2>
<table>
    <tr>
        <th>School Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($schools as $school): ?>
        <tr>
            <td><?= htmlspecialchars($school['school_name']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="schoolId" value="<?= $school['id'] ?>">
                    <input type="text" name="newSchoolName" placeholder="Edit name" required>
                    <button type="submit" name="editSchool">Edit</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="schoolId" value="<?= $school['id'] ?>">
                    <button type="submit" name="deleteSchool" onclick="return confirm('Are you sure?');">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>