<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $activityID = $_POST['activity_Id'];
    $title = $_POST['title'];
    $language = $_POST['language'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE programming_activity SET title = ?, language = ?, deadline = ? WHERE activityID = ?");
    $stmt->bind_param("sssi", $title, $language, $deadline, $activityID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>