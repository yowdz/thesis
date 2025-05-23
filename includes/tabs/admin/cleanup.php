<?php
// Authorization check
if ($_SESSION['role'] !== 'main_admin') {
    die("Access denied.");
}

$months = $_GET['months'] ?? 6;
$action = $_GET['action'] ?? 'view';

// Get inactive students
$inactiveStudents = getInactiveStudents($pdo, $months);

// Handle deletion
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = array_map('intval', $_POST['student_ids']);
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $_SESSION['success'] = "Deleted " . count($ids) . " inactive students.";
        header("Location: ?tab=cleanup&months=$months");
        exit;
    }
}
?>

<div class="cleanup-container">
    <h2>Student Cleanup Tool</h2>
    
    <form method="get" class="filter-form">
        <label>Show students inactive for:
            <select name="months">
                <option value="3" <?= $months == 3 ? 'selected' : '' ?>>3 months</option>
                <option value="6" <?= $months == 6 ? 'selected' : '' ?>>6 months</option>
                <option value="12" <?= $months == 12 ? 'selected' : '' ?>>1 year</option>
            </select>
        </label>
        <button type="submit">Filter</button>
    </form>

    <?php if (!empty($inactiveStudents)): ?>
    <form method="post" action="?tab=cleanup&months=<?= $months ?>&action=delete">
        <table class="cleanup-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Last Active</th>
                    <th>Days Inactive</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inactiveStudents as $student): 
                    $lastActive = new DateTime($student['created_at']); // Should be activity_date if you track it
                    $daysInactive = $lastActive->diff(new DateTime())->days;
                ?>
                <tr>
                    <td><input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>"></td>
                    <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['phone']) ?></td>
                    <td><?= $lastActive->format('M j, Y') ?></td>
                    <td><?= $daysInactive ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="delete-btn" 
                onclick="return confirm('Permanently delete selected students?')">
            Delete Selected
        </button>
    </form>
    <?php else: ?>
    <p class="no-results">No inactive students found for this period.</p>
    <?php endif; ?>
</div>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('input[name="student_ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>