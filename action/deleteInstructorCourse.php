<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructorCourseID = $_POST['instructor_courseID'];

    $query = $conn->prepare("DELETE FROM instructor_courses WHERE instructor_courseID = ?");
    $query->bind_param("i", $instructorCourseID);
    if($query->execute()){
        echo "success";
    } else {
        echo "failed";
    }
}
?>