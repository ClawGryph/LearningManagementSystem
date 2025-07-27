<?php
include '../db.php';

$courseID = $_GET['courseID'] ?? null;

if ($courseID) {
    $stmt = $conn->prepare("SELECT CONCAT(courseCode, ' - ', courseName) FROM courses WHERE courseID = ?");
    $stmt->bind_param("i", $courseID);
    $stmt->execute();
    $stmt->bind_result($courseName);
    if ($stmt->fetch()) {
        echo htmlspecialchars($courseName);
    } else {
        echo 'Unknown Course';
    }
    $stmt->close();
} else {
    echo 'No Course ID Provided';
}
?>
