<?php
include '../db.php';

if (isset($_POST['student_id'], $_POST['instructor_courseID'])) {
    $studentId = intval($_POST['student_id']);
    $courseId = intval($_POST['instructor_courseID']);

    $stmt = $conn->prepare("
        DELETE FROM instructor_student_load
        WHERE studentID = ? AND instructor_courseID = ?
    ");
    $stmt->bind_param("ii", $studentId, $courseId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>
