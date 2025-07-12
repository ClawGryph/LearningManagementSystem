<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instructorCourseID = $_POST['instructor_courseID'];
    $newCourseCode = $_POST['courseCode'];
    $newCourseName = $_POST['courseName'];
    $newInstructorID = $_POST['instructorID'];

    // Step 1: Get the courseID from instructor_courses
    $stmt = $conn->prepare("SELECT courseID FROM instructor_courses WHERE instructor_courseID = ?");
    $stmt->bind_param("i", $instructorCourseID);
    $stmt->execute();
    $stmt->bind_result($courseID);
    $stmt->fetch();
    $stmt->close();

    if (!$courseID) {
        echo "Invalid course";
        exit;
    }

    // Step 2: Update the courses table
    $stmt = $conn->prepare("UPDATE courses SET courseCode = ?, courseName = ? WHERE courseID = ?");
    $stmt->bind_param("ssi", $newCourseCode, $newCourseName, $courseID);
    $success1 = $stmt->execute();
    $stmt->close();

    // Step 3: Update the instructor_courses table
    $stmt = $conn->prepare("UPDATE instructor_courses SET instructorID = ? WHERE instructor_courseID = ?");
    $stmt->bind_param("ii", $newInstructorID, $instructorCourseID);
    $success2 = $stmt->execute();
    $stmt->close();

    if ($success1 && $success2) {
        echo "success";
    } else {
        echo "failed";
    }
}
?>
