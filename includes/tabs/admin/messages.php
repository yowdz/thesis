<link rel="stylesheet" href="css/tblast.css">
<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

// Fetch students
$students = fetchStudents($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendBlast'])) {
    $message = trim($_POST['message']);
    $method = $_POST['method'] ?? [];
    $selectedStudents = $_POST['selectedStudents'] ?? [];

    if ($message !== '' && count($method) > 0 && count($selectedStudents) > 0) {
        foreach ($selectedStudents as $studentId) {
            $student = getStudentById($pdo, $studentId);
            if (!$student) continue;

            $contactMethods = [];

            // SMS
            if (in_array('sms', $method) && !empty($student['phone'])) {
                $smsResult = sendSMS($student['phone'], $message);
                if ($smsResult['status'] === 'success') {
                    $contactMethods[] = 'sms';
                }
            }

            // Email
            if (in_array('email', $method) && !empty($student['email'])) {
                $emailResult = sendEmail($student['email'], 'Important Update', $message);
                if ($emailResult) {
                    $contactMethods[] = 'email';
                }
            }

            // Smart status update (preserves previous contact methods)
            if (!empty($contactMethods)) {
                $currentStatus = $student['contacted_via'] ?? 'none';
                $newStatus = $currentStatus;
                
                if (in_array('sms', $contactMethods)) {
                    $newStatus = ($newStatus === 'email' || $newStatus === 'both') ? 'both' : 'sms';
                }
                if (in_array('email', $contactMethods)) {
                    $newStatus = ($newStatus === 'sms' || $newStatus === 'both') ? 'both' : 'email';
                }

                // Only update if status changed
                if ($newStatus !== $currentStatus) {
                    $pdo->prepare("UPDATE students SET contacted_via = ? WHERE id = ?")
                        ->execute([$newStatus, $studentId]);
                }
            }
        }

        $_SESSION['success'] = "Blast sent successfully!";
        header("Location: dashboard.php?tab=messages");
        exit();
    } else {
        $_SESSION['error'] = "Please fill out the message and select students and a method.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Blast</title>
</head>
<body>
    <h1>Message Blast</h1>

    <div class="filter-container">
        <label for="filterSelect">Filter:</label>
        <select id="filterSelect" aria-label="Filter students by contact status">
            <option value="all">All</option>
            <option value="none">Not Contacted</option>
            <option value="sms">SMS Sent</option>
            <option value="email">Email Sent</option>
            <option value="both">Both Sent</option>
        </select>
        <button type="button" id="applyFilter" aria-label="Apply selected filter to students">Apply Filter</button>
    </div>

    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Message Blast Form -->
    <form method="POST" action="">
        <textarea name="message" placeholder="Enter your message here..." required></textarea><br>
        <label><input type="checkbox" name="method[]" value="sms"> SMS</label>
        <label><input type="checkbox" name="method[]" value="email"> Email</label><br>
        <button type="submit" name="sendBlast">Send Blast</button><br><br>

        <input type="text" id="searchInput" placeholder="Search students..."><br>

        <!-- Students Table -->
        <div class="table-wrapper">
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">First Name</th>
                        <th onclick="sortTable(1)">Middle Name</th>
                        <th onclick="sortTable(2)">Last Name</th>
                        <th onclick="sortTable(3)">Phone</th>
                        <th onclick="sortTable(4)">Email</th>
                        <th onclick="sortTable(5)">Course 1</th>
                        <th onclick="sortTable(6)">Course 2</th>
                        <th onclick="sortTable(7)">Course 3</th>
                        <th onclick="sortTable(8)">School</th>
                        <th onclick="sortTable(9)">LRN</th>
                        <th onclick="sortTable(10)">Contact Status</th>
                        <th onclick="sortTable(11)">Enrollment Status</th>
                        <th><input type="checkbox" id="selectAll"> Select</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['middle_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['phone']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['course1']) ?></td>
                        <td><?= htmlspecialchars($student['course2']) ?></td>
                        <td><?= htmlspecialchars($student['course3']) ?></td>
                        <td><?= htmlspecialchars($student['school_id']) ?></td>
                        <td><?= htmlspecialchars($student['lrn']) ?></td>
                        <td>
                            <?= match($student['contacted_via'] ?? 'none') {
                                'none' => 'Not contacted',
                                'sms' => '📱 SMS sent',
                                'email' => '📧 Email sent',
                                'both' => '📱📧 Both sent',
                                default => 'Unknown'
                            } ?>
                        </td>
                        <td><?= htmlspecialchars($student['status'] ?? 'Pending') ?></td>
                        <td><input type="checkbox" name="selectedStudents[]" value="<?= $student['id'] ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <script>
        // Search feature
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('#studentsTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(searchValue) ? '' : 'none';
            });
        });

        // Sort feature
        const sortDirections = {};
        function sortTable(columnIndex) {
            const table = document.getElementById('studentsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            sortDirections[columnIndex] = !sortDirections[columnIndex];
            
            rows.sort((a, b) => {
                const cellA = a.cells[columnIndex].innerText.trim().toLowerCase();
                const cellB = b.cells[columnIndex].innerText.trim().toLowerCase();
                return sortDirections[columnIndex] 
                    ? cellA.localeCompare(cellB) 
                    : cellB.localeCompare(cellA);
            });
            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }

        // Select All feature
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('input[name="selectedStudents[]"]')
                .forEach(cb => cb.checked = this.checked);
        });

        // Filter Select-All feature
        document.getElementById('applyFilter').addEventListener('click', function () {
            const filterValue = document.getElementById('filterSelect').value.toLowerCase();
            const rows = document.querySelectorAll('#studentsTable tbody tr');

            rows.forEach(row => {
                const statusCell = row.cells[10]?.innerText.trim().toLowerCase(); // Ensure cell exists and trim whitespace
                const checkbox = row.querySelector('input[name="selectedStudents[]"]');

                if (!statusCell || !checkbox) {
                    return; // Skip rows without a valid status cell or checkbox
                }

                // Apply filter logic
                if (filterValue === 'all') {
                    checkbox.checked = true; // Check all
                    row.classList.add('highlighted'); // Optional: Highlight the row
                } else if (filterValue === 'none' && statusCell.includes('not contacted')) {
                    checkbox.checked = true;
                    row.classList.add('highlighted');
                } else if (filterValue === 'sms' && statusCell.includes('sms sent')) {
                    checkbox.checked = true;
                    row.classList.add('highlighted');
                } else if (filterValue === 'email' && statusCell.includes('email sent')) {
                    checkbox.checked = true;
                    row.classList.add('highlighted');
                } else if (filterValue === 'both' && statusCell.includes('both sent')) {
                    checkbox.checked = true;
                    row.classList.add('highlighted');
                } else {
                    checkbox.checked = false; // Uncheck if it doesn't match the filter
                    row.classList.remove('highlighted'); // Optional: Remove highlight
                }
            });
        });
    </script>
</body>
</html>