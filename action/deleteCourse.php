<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $courseID = $_POST['course_Id'];

    $stmt = $conn->prepare("DELETE FROM courses WHERE courseID = ?");
    $stmt->bind_param("i", $courseID);
    if($stmt->execute()){
        echo "success";
    } else {
        echo "<script>alert('Deletion failed.'); window.history.back();</script>";
    }
}
?>