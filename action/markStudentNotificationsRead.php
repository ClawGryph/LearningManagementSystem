<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notifications'])) {
    $notifications = $_POST['notifications']; // Array of lmIDs

    $placeholders = implode(',', array_fill(0, count($notifications), '?'));
    $types = str_repeat('i', count($notifications));

    $stmt = $conn->prepare("UPDATE student_assessments SET is_read = 1 WHERE record_id IN ($placeholders)");
    $stmt->bind_param($types, ...$notifications);
    $stmt->execute();

    header('Location: ../student/student-landingpage.php'); // Redirect back
    exit;
}
