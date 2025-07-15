<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $courseID = $_POST['course_Id'];
    $courseCode = $_POST['courseCode'];
    $courseName = $_POST['courseName'];

    $stmt = $conn->prepare("UPDATE courses SET courseCode = ?, courseName = ? WHERE courseID = ?");
    $stmt->bind_param("ssi", $courseCode, $courseName, $courseID);
    if($stmt->execute()){
        echo "<script>alert('Update successful.'); window.location.href='../admin-create-courses.php';</script>";
    }else{
        echo "<script>alert('Update failed.'); window.history.back();</script>";
    }
}
?>