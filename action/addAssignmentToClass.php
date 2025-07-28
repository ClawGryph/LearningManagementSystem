<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $assignmentID = $_POST['assignmentID'];
    $assessmentType = 'assignment';

    $checkStmt = $conn->prepare("SELECT aa.instructor_courseID, aa.assessment_refID, aa.assessment_type FROM assessment_author aa JOIN programming_activity pa ON aa.assessment_refID = pa.activityID WHERE instructor_courseID = ? AND assessment_refID = ? AND assessment_type = ?");
    $checkStmt->bind_param("iis", $instructor_courseID, $assignmentID, $assessmentType);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This assignment is already added to the selected class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        // Step 1: Insert into assessment_author
        $stmt = $conn->prepare("
            INSERT INTO assessment_author (instructor_courseID, assessment_type, assessment_refID) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("isi", $instructor_courseID, $assessmentType, $assignmentID);

        if ($stmt->execute()) {
            echo "<script>alert('Successfully added to class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
        } else {
            echo "<script>alert('Failed to add the assignment to class: " . htmlspecialchars($stmt->error) . "');</script>";
        }

        $stmt->close();
    }

    $checkStmt->close();
}
?>