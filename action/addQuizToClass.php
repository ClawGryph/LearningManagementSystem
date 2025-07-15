<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $quizID = $_POST['quizID'];
    $quizTime = $_POST['quizTime'];

    $stmt = $conn->prepare("INSERT INTO quiz_author (instructor_courseID, quizID, quizTime) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $instructor_courseID, $quizID, $quizTime);
    if($stmt->execute()){
        echo "sucess";
    }else{
        echo "failed";
    }

    $stmt->close();
}
?>