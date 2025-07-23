<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notifications'])) {
    $notifications = $_POST['notifications']; // Array of lmIDs

    $placeholders = implode(',', array_fill(0, count($notifications), '?'));
    $types = str_repeat('i', count($notifications));

    $stmt = $conn->prepare("UPDATE learningmaterials_author SET is_read = 1 WHERE lmID IN ($placeholders)");
    $stmt->bind_param($types, ...$notifications);
    $stmt->execute();

    header('Location: ../admin/admin-landingpage.php'); // Redirect back
    exit;
}
