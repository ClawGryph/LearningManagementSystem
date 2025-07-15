<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_instructor'])) {
    $instructorId = $_POST['instructor_id'];
    $courseId = $_POST['course_id'];
    $classId = $_POST['class_id'];

    // Generate a random 8-character alphanumeric code
    $generatedCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // Check if the instructor is already assigned to the course
    $checkStmt = $conn->prepare("SELECT * FROM instructor_courses WHERE instructorID = ? AND courseID = ? AND classID = ?");
    $checkStmt->bind_param("iii", $instructorId, $courseId, $classId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Instructor is already assigned to this class.'); window.history.back();</script>";
    } else {
        // Insert into instructor_courses table
        $insertStmt = $conn->prepare("INSERT INTO instructor_courses (instructorID, courseID, classID, code) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("iiis", $instructorId, $courseId, $classId, $generatedCode);
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