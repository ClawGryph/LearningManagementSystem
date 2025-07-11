<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_instructor'])) {
    $instructorId = $_POST['instructor_id'];
    $courseId = $_POST['course_id'];

    // Generate a random 8-character alphanumeric code
    $generatedCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // Check if the instructor is already assigned to the course
    $checkStmt = $conn->prepare("SELECT * FROM instructor_courses WHERE instructorID = ? AND courseID = ?");
    $checkStmt->bind_param("ii", $instructorId, $courseId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Instructor is already assigned to this course.'); window.history.back();</script>";
    } else {
        // Insert into instructor_courses table
        $insertStmt = $conn->prepare("INSERT INTO instructor_courses (instructorID, courseID, code) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iis", $instructorId, $courseId, $generatedCode);
        if ($insertStmt->execute()) {
            echo "<script>alert('Instructor added successfully.'); window.location.href='../admin/admin-landingpage.php'</script>";
        } else {
            echo "<script>alert('Error adding instructor.'); window.history.back();</script>";
        }
        $insertStmt->close();
    }
    $checkStmt->close();
}
?>