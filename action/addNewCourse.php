<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $courseCode = trim($_POST['courseCode']);
    $courseName = trim($_POST['courseName']);

    // Validate inputs
    if(empty($courseCode) || empty($courseName)){
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO courses (courseCode, courseName) VALUES (?, ?)");
        $stmt->bind_param("ss", $courseCode, $courseName);

        if($stmt->execute()){
            echo "<script>alert('Course created successfully!');</script>";
        } else {
            echo "<script>alert('Error creating course: " . htmlspecialchars($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}
?>