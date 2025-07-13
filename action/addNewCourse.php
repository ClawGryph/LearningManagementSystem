<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseCode = trim($_POST['courseCode']);
    $courseName = trim($_POST['courseName']);

    // Validate inputs
    if (empty($courseCode) || empty($courseName)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
    } else {
        // Check for duplicate courseCode
        $check = $conn->prepare("SELECT courseID FROM courses WHERE courseCode = ?");
        $check->bind_param("s", $courseCode);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<script>alert('Course with this code already exists.'); window.history.back();</script>";
        } else {
            // Proceed to insert
            $stmt = $conn->prepare("INSERT INTO courses (courseCode, courseName) VALUES (?, ?)");
            $stmt->bind_param("ss", $courseCode, $courseName);

            if ($stmt->execute()) {
                echo "<script>alert('Course created successfully!'); window.location.href='../admin/admin-landingpage.php';</script>";
            } else {
                echo "<script>alert('Error creating course: " . htmlspecialchars($stmt->error) . "');</script>";
            }
            $stmt->close();
        }

        $check->close();
    }
}
?>