<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profileImage'])) {
    $file = $_FILES['profileImage'];
    $uploadDir = '../uploads/';
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        echo "Invalid file type.";
        exit;
    }

    $newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Update user profileImage column
        $stmt = $conn->prepare("UPDATE users SET profileImage = ? WHERE userID = ?");
        $stmt->bind_param("si", $newFileName, $userId);
        $stmt->execute();
        echo "success:" . $newFileName;
    } else {
        echo "Failed to upload image.";
    }
}
?>
