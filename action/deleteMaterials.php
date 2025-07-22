<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $lmID = $_POST['lm_Id'];

    $stmt = $conn->prepare("DELETE FROM course_learningmaterials WHERE course_lmID = ?");
    $stmt->bind_param("i", $lmID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>