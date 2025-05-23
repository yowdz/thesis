<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function fetchStudents($pdo) {
    try {
        $sql = "SELECT s.*, 
                       sch.school_name,
                       c1.course_name as course1_name,
                       c2.course_name as course2_name,
                       c3.course_name as course3_name
                FROM students s
                LEFT JOIN schools sch ON s.school_id = sch.id
                LEFT JOIN courses c1 ON s.course1 = c1.id
                LEFT JOIN courses c2 ON s.course2 = c2.id
                LEFT JOIN courses c3 ON s.course3 = c3.id
                ORDER BY s.last_name, s.first_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching students: " . $e->getMessage());
        return [];
    }
}

function fetchSchools($pdo) {
    $stmt = $pdo->query("SELECT * FROM schools");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchTopSchools($pdo) {
    try {
        $sql = "SELECT s.school_name, COUNT(st.id) as student_count
                FROM schools s
                LEFT JOIN students st ON s.id = st.school_id
                GROUP BY s.id, s.school_name
                ORDER BY student_count DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching top schools: " . $e->getMessage());
        return [];
    }
}


// Updated: Detailed course report with "Interested" and "Enrolled" breakdown
function fetchCourseReports($pdo) {
    try {
        $sql = "
            SELECT 
                c.id AS course_id,
                c.course_name,
                SUM(CASE WHEN sc.status = 'Interested' THEN 1 ELSE 0 END) AS interested_count,
                SUM(CASE WHEN sc.status = 'Enrolled' THEN 1 ELSE 0 END) AS enrolled_count,
                COUNT(DISTINCT sc.student_id) AS student_count
            FROM courses c
            LEFT JOIN (
                SELECT id AS student_id, course1 AS course_id, status FROM students WHERE course1 IS NOT NULL
                UNION ALL
                SELECT id AS student_id, course2 AS course_id, status FROM students WHERE course2 IS NOT NULL
                UNION ALL
                SELECT id AS student_id, course3 AS course_id, status FROM students WHERE course3 IS NOT NULL
            ) sc ON c.id = sc.course_id
            GROUP BY c.id, c.course_name
            ORDER BY student_count DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching course reports: " . $e->getMessage());
        return [];
    }
}


// New: Fetch overall student count
function fetchTotalStudents($pdo) {
    try {
        $sql = "SELECT COUNT(*) as total FROM students";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching total students: " . $e->getMessage());
        return ['total' => 0];
    }
}
// New: Fetch status breakdown report
function fetchStatusReports($pdo) {
    try {
        $sql = "SELECT status, COUNT(*) as count 
                FROM students 
                GROUP BY status";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching status reports: " . $e->getMessage());
        return [];
    }
}

// Renders the student table
function renderStudentsTable($students) {
    echo '<table id="studentTable">
        <tr>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>School</th>
            <th>LRN</th>
            <th>Course 1</th>
            <th>Course 2</th>
            <th>Course 3</th>
            <th>Status</th>
        </tr>';
    foreach ($students as $student) {
        echo '<tr>
            <td>' . htmlspecialchars($student['first_name']) . '</td>
            <td>' . htmlspecialchars($student['middle_name'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($student['last_name']) . '</td>
            <td>' . htmlspecialchars($student['email']) . '</td>
            <td>' . htmlspecialchars($student['phone']) . '</td>
            <td>' . htmlspecialchars($student['school_name']) . '</td>
            <td>' . htmlspecialchars($student['lrn']) . '</td>
            <td>' . htmlspecialchars($student['course1']) . '</td>
            <td>' . htmlspecialchars($student['course2'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($student['course3'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($student['status'] ?? 'N/A') . '</td>
        </tr>';
    }
    echo '</table>';
}

// Renders the school table
function renderSchoolsTable($schools) {
    echo '<table id="schoolTable">
        <tr>
            <th>School Name</th>
        </tr>';
    foreach ($schools as $school) {
        echo '<tr>
            <td>' . htmlspecialchars($school['school_name']) . '</td>
        </tr>';
    }
    echo '</table>';
}

// Add a new school to the database
function addSchool($pdo, $schoolName) {
    $stmt = $pdo->prepare("INSERT INTO schools (school_name) VALUES (:school_name)");
    $stmt->execute(['school_name' => $schoolName]);
}

// Edit an existing school's name
function editSchool($pdo, $schoolId, $newSchoolName) {
    $stmt = $pdo->prepare("UPDATE schools SET school_name = :school_name WHERE id = :id");
    $stmt->execute(['school_name' => $newSchoolName, 'id' => $schoolId]);
}

// Delete a school
function deleteSchool($pdo, $schoolId) {
    $stmt = $pdo->prepare("DELETE FROM schools WHERE id = :id");
    $stmt->execute(['id' => $schoolId]);
}

function getStudentById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}







function sendSMS($to, $message) {
    // Check if the phone number starts with "09" and prepend the country code
    if (substr($to, 0, 1) == '0') {
        $to = '+63' . substr($to, 1); // Replace the first "0" with "+63"
    }    

    $apiToken = '1702|GqzdmFV5HbU4Jn6ujXYQLHWwfo5l3ajwImlI2gAR'; // Replace with your actual API token
    $senderId = 'PhilSMS'; // Sender ID
    $apiUrl = 'https://app.philsms.com/api/v3/sms/send'; // API URL

    // Prepare the data
    $send_data = [
        'sender_id' => $senderId,
        'recipient' => $to, // Ensure this is in the format +639XXXXXXXXX
        'message' => $message
    ];

    // JSON encode the data
    $parameters = json_encode($send_data);

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set headers for authorization and content type
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $apiToken"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $get_sms_status = curl_exec($ch);

    // Check for cURL errors
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['status' => 'error', 'message' => $err];
    }

    // Decode response to handle it
    $responseData = json_decode($get_sms_status, true);

    if (isset($responseData['status']) && $responseData['status'] === 'success') {
        return ['status' => 'success', 'data' => $responseData['data']];
    } else {
        $errorMessage = $responseData['message'] ?? 'Unknown error';
        return ['status' => 'error', 'message' => $errorMessage];
    }
}

function fetchCourses($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY course_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching courses: " . $e->getMessage());
        return [];
    }
}

function sendEmail($email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // or your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'enechan809@gmail.com'; // your email
        $mail->Password = 'qjkd anbb sfay zoll'; // use App Password if it's Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email setup
        $mail->setFrom('enechan809@gmail.com', 'Eugene Ponce de Leon');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}

function getInactiveStudents(PDO $pdo, int $months = 6): array {
    $cutoffDate = (new DateTime())->modify("-$months months")->format('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("
        SELECT s.* 
        FROM students s
        WHERE s.status = 'Interested'
        AND s.created_at < ?
        AND NOT EXISTS (
            SELECT 1 FROM student_activity 
            WHERE student_id = s.id 
            AND activity_date > DATE_SUB(NOW(), INTERVAL ? MONTH)
    ");
    $stmt->execute([$cutoffDate, $months]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cleanupOldEnrolledStudents(PDO $pdo, int $months = 3): void {
    $cutoff = (new DateTime())->modify("-$months months")->format('Y-m-d');
    
    // Columns that exist in both tables
    $commonColumns = [
        'id', 'first_name', 'middle_name', 'last_name', 'email', 'phone',
        'school_id', 'lrn', 'course1', 'course2', 'course3', 'created_at',
        'status', 'contacted_via'
    ];
    $columnList = implode(', ', $commonColumns);
    
    $pdo->beginTransaction();
    try {
        // 1. Archive - using CURRENT_TIMESTAMP for archived_at
        $archiveStmt = $pdo->prepare("
            INSERT INTO students_archive ($columnList, archived_at) 
            SELECT $columnList, CURRENT_TIMESTAMP() 
            FROM students 
            WHERE status = 'Enrolled' AND created_at < ?
        ");
        $archiveStmt->execute([$cutoff]);
        
        // 2. Delete
        $deleteStmt = $pdo->prepare("
            DELETE FROM students 
            WHERE status = 'Enrolled' 
            AND created_at < ?
        ");
        $deleteStmt->execute([$cutoff]);
        
        $pdo->commit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Cleanup failed: " . $e->getMessage());
        throw $e; // Re-throw to handle in calling code
    }
}