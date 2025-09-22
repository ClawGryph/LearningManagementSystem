<?php
include '../db.php';
session_start();
require 'emailConfig.php';
header('Content-Type: application/json');

// Ensure user is verified (e.g., via email session)
if (empty($_SESSION['reset_email'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized request"]);
    exit;
}

if (!isset($_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "No password provided"]);
    exit;
}

$email = $_SESSION['reset_email'];
$hashedEmail = hashEmail($email);
$newPassword = $_POST['password'];

// Hash the password securely
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// Update the database
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $hashedEmail);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
    // clear reset session
    unset($_SESSION['reset_email']);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password"]);
}
$stmt->close();
$conn->close();
?>