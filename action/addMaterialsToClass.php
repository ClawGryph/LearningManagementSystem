<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $lmID = $_POST['course_lmID'];

    $checkStmt = $conn->prepare("SELECT * FROM learningmaterials_author WHERE instructor_courseID = ? AND course_lmID = ?");
    $checkStmt->bind_param("ii", $instructor_courseID, $lmID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This quiz is already added to the selected class.'); window.history.back();</script>";
    } else {
        // Step 2: Insert if not yet added
        $stmt = $conn->prepare("INSERT INTO learningmaterials_author (instructor_courseID, course_lmID) VALUES (?, ?)");
        $stmt->bind_param("ii", $instructor_courseID, $lmID);

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