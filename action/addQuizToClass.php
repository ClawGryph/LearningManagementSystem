<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $quizID = $_POST['quizID'];
    $quizTime = $_POST['quizTime'];

    $checkStmt = $conn->prepare("SELECT * FROM quiz_author WHERE instructor_courseID = ? AND quizID = ?");
    $checkStmt->bind_param("ii", $instructor_courseID, $quizID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This quiz is already added to the selected class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        // Step 2: Insert if not yet added
        $stmt = $conn->prepare("INSERT INTO quiz_author (instructor_courseID, quizID, quizTime) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $instructor_courseID, $quizID, $quizTime);

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