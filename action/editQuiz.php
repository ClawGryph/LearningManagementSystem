<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $quizID = $_POST['quiz_Id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, deadline = ? WHERE quizID = ?");
    $stmt->bind_param("sssi", $title, $description, $deadline, $quizID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>