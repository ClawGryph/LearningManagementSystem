<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $assignmentID = $_POST['assignment_Id'];
    $title = $_POST['title'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE assignment SET title = ?, deadline = ? WHERE assignmentID = ?");
    $stmt->bind_param("ssi", $title, $deadline, $assignmentID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>