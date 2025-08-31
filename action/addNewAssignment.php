<?php
session_start();
include '../db.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['assignmentTitle'];
    $description = $_POST['assignmentDescription'];
    $deadline = $_POST['assignmentDeadline'];
    $maxScore = $_POST['max_score'];

    $instructor_ID = $_SESSION['user_id'];

    // Handle file upload
    $file = $_FILES['assignmentFile'];
    $fileName = basename($file['name']);
    $uploadDir = '../uploads/assignments/';
    $targetPath = $uploadDir . $fileName;

    // Create uploads folder if not exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Save assignment info in database
        $stmt = $conn->prepare("INSERT INTO assignment (instructor_ID, title, description, file_path, max_score, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssis", $instructor_ID, $title, $description, $fileName, $maxScore, $deadline);

        if ($stmt->execute()) {
            echo "<script>alert('Assignment uploaded successfully!'); window.location.href = '../instructor/instructor-landingpage.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to upload file.";
    }

    $conn->close();
}
?>
