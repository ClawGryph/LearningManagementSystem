<?php
session_start();
include '../db.php';
require 'emailConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = trim($_POST['emailCode']);

    if (!isset($_SESSION['verification_code']) || $enteredCode != $_SESSION['verification_code']) {
        echo json_encode(["status" => "error", "message" => "Invalid verification code"]);
        exit;
    }

    // Get stored signup data
    $data = $_SESSION['signup_data'];

    $role = strtolower(trim($data['role']));
    $firstName = $data['firstname'];
    $lastName = $data['lastName'];
    $hashedEmail = $data['email'];
    $password = $data['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (role, firstName, lastName, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $role, $firstName, $lastName, $hashedEmail, $hashedPassword);

    if ($stmt->execute()) {
        unset($_SESSION['verification_code']);
        unset($_SESSION['signup_data']);
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }
}
?>