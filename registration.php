<?php
require 'config.php';

// Fetch schools with both id and name
$schools = $pdo->query("SELECT id, school_name FROM schools ORDER BY school_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch courses from database
$courses = $pdo->query("SELECT course_name FROM courses ORDER BY course_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Error messages handling
$errorMessages = [
    'missing_fields' => 'Please fill in all required fields.',
    'duplicate' => 'This email or phone number is already registered.',
    'invalid_lrn' => 'Invalid LRN. It must be a 12-digit number.',
    'invalid_email' => 'Invalid email format.',
    'invalid_phone' => 'Invalid phone number. It must be 10 or 11 digits.',
    'db' => 'Database error. Please try again later.',
    'failed' => 'Registration failed. Please try again.'
];

// Handle success and error messages
$successMessage = isset($_GET['success']) ? "Registration successful!" : "";
if (isset($_GET['error'])) {
    if (isset($errorMessages[$_GET['error']])) {
        $errorMessage = $errorMessages[$_GET['error']];
    } else {
        $errorMessage = "An unknown error occurred. Please try again.";
    }
} else {
    $errorMessage = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/registration.css">
</head>
<body>
    
<div class="container mt-5">
    <!-- Logo Section -->
    <div class="text-center mb-4">
        <img src="bg/oc.png" alt="Olivarez College Logo" class="logo-img">
        <h1 class="text-center mb-4">Olivarez College</h1>
    </div>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Pre-Enrollment Interest Form</h1>

        <!-- Success & Error Messages -->
        <?php if ($successMessage) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                <?= htmlspecialchars($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="process_registration.php" method="POST" onsubmit="return validateCourses()">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="firstName" class="form-label">First Name*</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                </div>
                <div class="col-md-4">
                    <label for="lastName" class="form-label">Last Name*</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                </div>
                <div class="col-md-4">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middleName">
                </div>
            </div>

            <!-- School Selection -->
            <div class="mb-3">
                <label for="school" class="form-label">School*</label>
                <div class="input-group">
                    <select class="form-select" id="school" name="school_id" required>
                        <option value="">Select School</option>
                        <?php foreach ($schools as $school) : ?>
                            <option value="<?= htmlspecialchars($school['id']) ?>">
                                <?= htmlspecialchars($school['school_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                        Add School
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="lrn" class="form-label">Learner Reference Number (LRN)*</label>
                <input type="text" class="form-control" id="lrn" name="lrn" required pattern="\d{12}" title="12-digit LRN">
            </div>

            <!-- Course Selection -->
            <div class="mb-3">
                <label for="course1" class="form-label">Interested Course (1st Choice) *</label>
                <select class="form-select" id="course1" name="course1" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?= htmlspecialchars($course['course_name']) ?>">
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="course2" class="form-label">Interested Course (2nd Choice)</label>
                <select class="form-select" id="course2" name="course2">
                    <option value="">None</option>
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?= htmlspecialchars($course['course_name']) ?>">
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="course3" class="form-label">Interested Course (3rd Choice)</label>
                <select class="form-select" id="course3" name="course3">
                    <option value="">None</option>
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?= htmlspecialchars($course['course_name']) ?>">
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Contact Information -->
            <div class="mb-3">
                <label for="email" class="form-label">Email*</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Contact Number*</label>
                <input type="tel" class="form-control" id="phone" name="phone" required pattern="\d{10,11}" title="10 or 11 digit number">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <!-- Add School Modal -->
    <div class="modal fade" id="addSchoolModal" tabindex="-1" aria-labelledby="addSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSchoolModalLabel">Add New School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSchoolForm">
                        <div class="mb-3">
                            <label for="newSchoolName" class="form-label">School Name</label>
                            <input type="text" class="form-control" id="newSchoolName" name="newSchoolName" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add School</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss success alert after 4 seconds
        window.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if(alert) {
                setTimeout(() => {
                    // Use Bootstrap JS to close the alert
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 4000);
            }
        });

        // Course validation (keep your existing function)
        function validateCourses() {
            let c1 = document.getElementById("course1").value;
            let c2 = document.getElementById("course2").value;
            let c3 = document.getElementById("course3").value;

            if ((c2 && c1 === c2) || (c3 && (c1 === c3 || c2 === c3))) {
                alert("You cannot select the same course multiple times.");
                return false;
            }
            return true;
        }

        // Add School functionality
        document.getElementById('addSchoolForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const schoolNameInput = document.getElementById('newSchoolName');
            const schoolName = schoolNameInput.value.trim();

            if (!schoolName) {
                alert("Please enter a valid school name.");
                return;
            }

            fetch('add_school.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ schoolName: schoolName })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Add to dropdown
                    const schoolDropdown = document.getElementById('school');
                    const newOption = new Option(data.school.school_name, data.school.id);
                    schoolDropdown.add(newOption);

                    // Sort dropdown while keeping first option
                    const options = Array.from(schoolDropdown.options);
                    const firstOption = options.shift();
                    options.sort((a, b) => a.text.localeCompare(b.text));

                    schoolDropdown.innerHTML = '';
                    schoolDropdown.appendChild(firstOption);
                    options.forEach(option => schoolDropdown.appendChild(option));

                    // Select the new school
                    schoolDropdown.value = data.school.id;

                    // Close and reset modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSchoolModal'));
                    modal.hide();
                    schoolNameInput.value = '';

                    // Show success message
                    alert(data.message);
                } else {
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        });
    </script>
</body>
</html>