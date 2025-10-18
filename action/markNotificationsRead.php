<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update all unread notifications to read
    $stmt = $conn->prepare("UPDATE learningmaterials_author SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();

    echo 'success';
}
?>