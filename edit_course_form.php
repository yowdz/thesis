<form action="edit_course.php" method="POST">
    <input type="hidden" name="courseId" value="<!-- PHP: echo $courseId -->">
    <label>Course Name:</label>
    <input type="text" name="courseName" value="<!-- PHP: echo $currentCourseName -->">

    <label>Admin Username:</label>
    <input type="text" name="adminUsername" value="<!-- PHP: echo $currentAdminUsername -->">

    <label>New Password (leave blank if not changing):</label>
    <input type="password" name="adminPassword">

    <button type="submit">Update</button>
</form>
