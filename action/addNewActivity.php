<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'];
    $title = $_POST['title'];
    $instructions = $_POST['instructions'];
    $expectedOutput = $_POST['expected_output'];
    $deadline = $_POST['activityDeadline'];
    $maxScore = $_POST['max_score'];

    $instructorID = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO programming_activity (instructor_ID, title, language, instructions, expected_output, max_score, deadline)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssis", $instructorID, $title, $language, $instructions, $expectedOutput, $maxScore, $deadline);

    if ($stmt->execute()) {
        echo "<script>alert('Activity saved successfully'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
