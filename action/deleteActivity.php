<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $activityID = $_POST['activity_Id'];
    
    $stmt = $conn->prepare("DELETE FROM programming_activity WHERE activityID = ?");
    $stmt->bind_param("i", $activityID);
    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'failed: ' . $conn->error;
    }
}
?>