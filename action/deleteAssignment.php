<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $assignmentID = $_POST['assignment_Id'];

    $stmt = $conn->prepare("DELETE FROM assignment WHERE assignmentID = ?");
    $stmt->bind_param("i", $assignmentID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>