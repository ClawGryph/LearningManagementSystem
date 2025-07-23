<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $assignmentID = $_POST['assignmentID'];

    $checkStmt = $conn->prepare("SELECT * FROM assignment_author WHERE instructor_courseID = ? AND assignmentID = ?");
    $checkStmt->bind_param("ii", $instructor_courseID, $assignmentID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This assignment is already added to the selected class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        // Step 2: Insert if not yet added
        $stmt = $conn->prepare("INSERT INTO assignment_author (instructor_courseID, assignmentID) VALUES (?, ?)");
        $stmt->bind_param("ii", $instructor_courseID, $assignmentID);

        if ($stmt->execute()) {
            echo "<script>alert('Quiz successfully added to the class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
        } else {
            echo "<script>alert('Failed to add the quiz to class: " . htmlspecialchars($stmt->error) . "');</script>";
        }
        $stmt->close();
    }

    $checkStmt->close();
}
?>