document.addEventListener("DOMContentLoaded", function () {
    const course1 = document.getElementById("course1");
    const course2 = document.getElementById("course2");
    const course3 = document.getElementById("course3");

    function validateCourses() {
        if (
            (course2.value !== "" && course2.value === course1.value) ||
            (course3.value !== "" && (course3.value === course1.value || course3.value === course2.value))
        ) {
            alert("Each course choice must be different.");
            return false;
        }
        return true;
    }
// Delete selected students
document.getElementById('deleteSelected').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.student-select:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one student to delete.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedCheckboxes.length} selected student(s)?`)) {
        return;
    }

    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => 
        checkbox.closest('tr').dataset.id
    );

    const button = this;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Deleting...';

    fetch('delete_students.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ids: selectedIds
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            throw new Error(data.message || 'Error deleting students');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'An error occurred while deleting the students');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-trash"></i> Delete Selected';
    });
});
    function preventDuplicates(event) {
        const selectedCourse = event.target.value;

        if (selectedCourse !== "") {
            if (
                (event.target === course2 && selectedCourse === course1.value) ||
                (event.target === course3 && (selectedCourse === course1.value || selectedCourse === course2.value))
            ) {
                alert("This course has already been selected. Please choose a different one.");
                event.target.value = ""; // Reset the selection
            }
        }
    }

    // Attach event listeners
    course2.addEventListener("change", preventDuplicates);
    course3.addEventListener("change", preventDuplicates);

    // Attach validation to form submission
    document.querySelector("form").onsubmit = validateCourses;
});

// Handle adding a new school
 // NEW: Add School functionality
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
// Function to sort dropdown options alphabetically
function sortDropdown(dropdown) {
    let options = Array.from(dropdown.options);
    options.shift(); // Keep "Select School" at the top

    options.sort((a, b) => a.text.localeCompare(b.text));

    dropdown.innerHTML = ""; // Clear existing options
    dropdown.appendChild(new Option("Select School", "")); // Re-add the first option

    options.forEach(option => dropdown.appendChild(option)); // Append sorted options
}
// Update datetime every second
function updateDateTime() {
    const now = new Date();
    const formattedDate = now.getUTCFullYear() + '-' + 
                         String(now.getUTCMonth() + 1).padStart(2, '0') + '-' + 
                         String(now.getUTCDate()).padStart(2, '0') + ' ' + 
                         String(now.getUTCHours()).padStart(2, '0') + ':' + 
                         String(now.getUTCMinutes()).padStart(2, '0') + ':' + 
                         String(now.getUTCSeconds()).padStart(2, '0');
    document.querySelector('.datetime').innerHTML = '<i class="bi bi-clock"></i> ' + formattedDate;
}

// Start updating datetime when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateDateTime();
    setInterval(updateDateTime, 1000);
});