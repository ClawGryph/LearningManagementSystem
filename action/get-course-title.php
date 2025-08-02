<?php
include '../db.php';
session_start();

if (empty($_SESSION['courseID']) || empty($_SESSION['user_id'])) {
    echo "<script>alert('No course ID/user ID found.'); </script>";
    $courseName = null;
    return;
}

$courseID = $_SESSION['courseID'];
$instructorID = $_SESSION['user_id'];

// Fetch course name
$courseName = '';
$stmt = $conn->prepare("SELECT CONCAT(courseCode, ' - ', courseName) AS class_name FROM courses WHERE courseID = ?");
$stmt->bind_param("i", $courseID);
$stmt->execute();
$stmt->bind_result($courseName);

if (!$stmt->fetch()) {
    $courseName = null;
}

$stmt->close();
?>