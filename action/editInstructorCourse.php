<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instructorCourseID = $_POST['instructor_courseID'];
    $newCourseID = $_POST['courseID'];
    $newInstructorID = $_POST['instructorID'];
    $newClassID = $_POST['classID'];

    // Check for duplicate assignment
    $checkStmt = $conn->prepare("SELECT * FROM instructor_courses WHERE instructorID = ? AND courseID = ? AND classID = ? AND instructor_courseID != ?");
    $checkStmt->bind_param("iiii", $newInstructorID, $newCourseID, $newClassID, $instructorCourseID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "duplicate"; // Record already exists
        exit;
    }

    // Proceed with update if no duplicate
    $stmt = $conn->prepare("UPDATE instructor_courses SET instructorID = ?, courseID = ?, classID = ? WHERE instructor_courseID = ?");
    $stmt->bind_param("iiii", $newInstructorID, $newCourseID, $newClassID, $instructorCourseID);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failed";
    }

    $checkStmt->close();
    $stmt->close();
}
?>
