<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $classId = $_POST['class_Id'];
    $oldSection = $_POST['oldSection'];
    $section = $_POST['section'];
    $maxStudent = $_POST['maxStudent'];

    $stmt = $conn->prepare("UPDATE class SET section = ?, maxStudent = ? WHERE classID = ?");
    $stmt->bind_param("sii", $section, $maxStudent, $classId);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>