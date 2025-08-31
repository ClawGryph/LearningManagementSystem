<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'];
    $title = $_POST['title'];
    $instructions = $_POST['instructions'];
    $expectedOutput = $_POST['expected_output'];
    $deadline = $_POST['activityDeadline'];

    $instructorID = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO programming_activity (instructor_ID, title, language, instructions, expected_output, deadline)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $instructorID, $title, $language, $instructions, $expectedOutput, $deadline);

    if ($stmt->execute()) {
        echo "<script>alert('Activity saved successfully'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
