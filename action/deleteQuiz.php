<?php
    include '../db.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $quizID = $_POST['quizId'];

        $stmt = $conn->prepare("DELETE FROM quizzes WHERE quizID = ?");
        $stmt->bind_param("i", $quizID);
        if($stmt->execute()) {
            echo 'success';
        } else {
            echo 'failed: ' . $conn->error;
        }
    }
?>