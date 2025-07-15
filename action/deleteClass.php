<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $classId = $_POST['classId'];

    $stmt = $conn->prepare("DELETE FROM class WHERE classID = ?");
    $stmt->bind_param("i", $classId);
    if($stmt->execute()){
        echo "success";
    } else {
        echo "failed";
    }
}
?>