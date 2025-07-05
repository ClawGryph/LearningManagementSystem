<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstname']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = $_POST['signup-password'];
    $confirmPass = $_POST['confirmPass'];
    $role = $_SESSION['selected_role'] ?? null; // ✅ Get role from session

    // Validation
    if (!$role) {
        echo "<script>alert('No role selected. Please go back to select a role.'); window.location.href='../index.php';</script>";
        exit;
    }

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "<script>alert('User already exists.'); window.history.back();</script>";
        exit;
    }

    // Insert into DB
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (role, firstName, lastName, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $role, $firstName, $lastName, $email, $hashedPassword);

    if ($stmt->execute()) {
        //unset($_SESSION['selected_role']); // Clear the role after using it
        echo "<script>
                alert('Registration successful!');
                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('signup').reset();
                });
                window.location.href='../access.php';
            </script>";
    } else {
        echo "<script>alert('Something went wrong.'); window.history.back();</script>";
    }
}
?>