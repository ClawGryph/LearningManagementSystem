<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $classYear = trim($_POST['classYear']);
    $classSection = trim($_POST['classSection']);
    $classMaxStudent = trim($_POST['classMaxStudent']);

    $check = $conn->prepare("SELECT classID FROM class where section = ? AND year = ?");
    $check->bind_param("si", $classSection, $classYear);
    $check->execute();
    $check->store_result();

    if($check->num_rows() > 0){
        echo "<script>alert('Class with this section in this year already exists.'); window.history.back();</script>";
    }else{
        //Proceed to insert
        $stmt = $conn->prepare("INSERT INTO class (year, section, maxStudent) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $classYear, $classSection, $classMaxStudent);

        if($stmt->execute()){
            echo "<script>alert('Class created successfully!'); window.location.href='../admin/admin-landingpage.php';</script>";
        }else{
            echo "<script>alert('Error creating class: " . htmlspecialchars($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}
?>