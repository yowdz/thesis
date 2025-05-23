<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
<?php
require_once 'includes/auth.php';
if ($_SESSION['role'] !== 'main_admin') {
    die("Access denied.");
}

require_once 'includes/db.php';
require_once 'includes/functions.php';

// Fetch all data once
$students = fetchStudents($pdo);
$schools = fetchSchools($pdo);
$courses = fetchCourses($pdo);  // Add this line

?>

<div class="page-header">
    <h1>Manage Students</h1>
</div>

<div class="action-bar">
    <div class="left-controls">
        <button type="button" class="btn btn-success" onclick="openAddStudentModal()">
            <i class="bi bi-plus-circle"></i> Add Student
        </button>
        <button type="button" class="btn btn-danger" id="deleteSelected" style="display: none;">
            <i class="bi bi-trash"></i> Delete Selected
        </button>
    </div>
    
    <div class="search-bar">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" id="studentSearch" placeholder="Search students...">
        </div>
    </div>
    
    <div class="filter-controls">
        <select class="form-select" id="statusFilter">
            <option value="all">All Statuses</option>
            <option value="Interested">Interested</option>
            <option value="Enrolled">Enrolled</option>
        </select>
    </div>
</div>

<div class="table-responsive">
    <table id="studentTable" class="table table-hover">
        <thead>
            <tr>
                <th width="40px">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                </th>
                <th>Name</th>
                <th width="120px">Status</th>
                <th>Course</th>
                <th width="100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr data-id="<?= htmlspecialchars($student['id']) ?>"
                    data-status="<?= htmlspecialchars($student['status']) ?>"
                    data-student='<?= htmlspecialchars(json_encode($student)) ?>'>
                    <td>
                        <input type="checkbox" class="form-check-input student-select" name="selectedStudents[]" value="<?= $student['id'] ?>">
                    </td>
                    <td>
                        <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . 
                            ($student['middle_name'] ? ' ' . $student['middle_name'] : '')) ?>
                    </td>
                    <td>
                        <span class="status-badge <?= strtolower($student['status']) ?>">
                            <?= htmlspecialchars($student['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($student['course1_name']) ?></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="viewStudent(this)" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-primary" onclick="editStudent(this)" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentModalContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="studentForm" class="needs-validation" novalidate>
                    <input type="hidden" id="studentId" name="studentId">
                    
                    <!-- Personal Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                    <div class="invalid-feedback">Please enter first name</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middleName" name="middleName">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                    <div class="invalid-feedback">Please enter last name</div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                    <div class="invalid-feedback">Please enter a valid phone number</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Academic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">School</label>
                                    <select class="form-select" id="schoolName" name="school_id" required>
                                        <option value="">Select School</option>
                                        <?php foreach ($schools as $school): ?>
                                            <option value="<?= htmlspecialchars($school['id']) ?>">
                                                <?= htmlspecialchars($school['school_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a school</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">LRN</label>
                                    <input type="text" class="form-control" id="lrn" name="lrn" required>
                                    <div class="invalid-feedback">Please enter a valid LRN</div>
                                </div>
                            </div>

                            <!-- Course Selection -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">First Choice Course</label>
                                    <select class="form-select" id="course1" name="course1" required>
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= htmlspecialchars($course['id']) ?>">
                                                <?= htmlspecialchars($course['course_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a course</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Second Choice Course (Optional)</label>
                                    <select class="form-select" id="course2" name="course2">
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= htmlspecialchars($course['id']) ?>">
                                                <?= htmlspecialchars($course['course_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Third Choice Course (Optional)</label>
                                    <select class="form-select" id="course3" name="course3">
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= htmlspecialchars($course['id']) ?>">
                                                <?= htmlspecialchars($course['course_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Status Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Interested">Interested</option>
                                        <option value="Enrolled">Enrolled</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveStudent()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner Modal -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Please wait...</p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initialize select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-select');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateDeleteButton();
        });
    }

    // Initialize individual checkboxes
    document.querySelectorAll('.student-select').forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButton);
    });

    // Initialize search and filter
    document.getElementById('studentSearch').addEventListener('input', filterStudents);
    document.getElementById('statusFilter').addEventListener('change', filterStudents);

    // Initialize input validations
    initializeValidations();

    // Initialize delete button functionality
    const deleteButton = document.getElementById('deleteSelected');
    if (deleteButton) {
        deleteButton.addEventListener('click', deleteSelectedStudents);
    }
});

function initializeValidations() {
    // Phone number validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });
    }

    // LRN validation
    const lrnInput = document.getElementById('lrn');
    if (lrnInput) {
        lrnInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
        });
    }

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

function updateDeleteButton() {
    const selectedCount = document.querySelectorAll('.student-select:checked').length;
    const deleteBtn = document.getElementById('deleteSelected');
    if (deleteBtn) {
        deleteBtn.style.display = selectedCount > 0 ? 'inline-block' : 'none';
    }
}

function filterStudents() {
    const searchText = document.getElementById('studentSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        const rowText = row.textContent.toLowerCase();
        const rowStatus = row.dataset.status;
        const matchesSearch = rowText.includes(searchText);
        const matchesStatus = status === 'all' || rowStatus === status;
        
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

function viewStudent(button) {
    const studentData = JSON.parse(button.closest('tr').dataset.student);
    const modal = new bootstrap.Modal(document.getElementById('studentModal'));
    const content = document.getElementById('studentModalContent');
    
    content.innerHTML = generateStudentDetailsHTML(studentData);
    modal.show();
}

function generateStudentDetailsHTML(student) {
    return `
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Full Name:</strong> 
                    ${student.last_name}, ${student.first_name} ${student.middle_name || ''}
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong> ${student.status}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>First Choice Course:</strong> ${student.course1_name || 'None'}
                </div>
                <div class="col-md-6">
                    <strong>LRN:</strong> ${student.lrn}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Second Choice Course:</strong> ${student.course2_name || 'None'}
                </div>
                <div class="col-md-6">
                    <strong>Third Choice Course:</strong> ${student.course3_name || 'None'}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Phone:</strong> ${student.phone}
                </div>
                <div class="col-md-6">
                    <strong>Email:</strong> ${student.email}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>School:</strong> ${student.school_name}
                </div>
                <div class="col-md-6">
                    <strong>Date Applied:</strong> ${student.created_at}
                </div>
            </div>
        </div>
    `;
}

function editStudent(button) {
    const studentData = JSON.parse(button.closest('tr').dataset.student);
    document.getElementById('editModalTitle').textContent = 'Edit Student';
    populateForm(studentData);
    new bootstrap.Modal(document.getElementById('editStudentModal')).show();
}

function openAddStudentModal() {
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('editModalTitle').textContent = 'Add New Student';
    document.getElementById('studentForm').classList.remove('was-validated');
    new bootstrap.Modal(document.getElementById('editStudentModal')).show();
}

function populateForm(student) {
    document.getElementById('studentId').value = student.id || '';
    document.getElementById('firstName').value = student.first_name || '';
    document.getElementById('middleName').value = student.middle_name || '';
    document.getElementById('lastName').value = student.last_name || '';
    document.getElementById('email').value = student.email || '';
    document.getElementById('phone').value = student.phone || '';
    document.getElementById('schoolName').value = student.school_id || '';
    document.getElementById('lrn').value = student.lrn || '';
    document.getElementById('course1').value = student.course1 || '';
    document.getElementById('course2').value = student.course2 || '';
    document.getElementById('course3').value = student.course3 || '';
    document.getElementById('status').value = student.status || '';
}

function saveStudent() {
    const form = document.getElementById('studentForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const saveButton = document.querySelector('[onclick="saveStudent()"]');
    saveButton.disabled = true;
    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    const formData = new FormData(form);
    const id = document.getElementById('studentId').value;
    
    fetch(id ? 'update_student.php' : 'add_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the student data.');
    })
    .finally(() => {
        saveButton.disabled = false;
        saveButton.innerHTML = 'Save Changes';
    });
}

function deleteSelectedStudents() {
    if (!confirm('Are you sure you want to delete the selected students?')) {
        return;
    }

    const selectedIds = Array.from(document.querySelectorAll('.student-select:checked'))
        .map(checkbox => checkbox.closest('tr').dataset.id);

    if (selectedIds.length === 0) return;

    const button = document.getElementById('deleteSelected');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Deleting...';

    fetch('delete_students.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the students.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-trash"></i> Delete Selected';
    });
}</script>