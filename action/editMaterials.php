<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $lmID = $_POST['lm_Id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE course_learningmaterials SET name = ?, description = ? WHERE course_lmID = ?");
    $stmt->bind_param("ssi", $title, $description, $lmID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>