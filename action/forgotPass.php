<?php
session_start();
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = strtolower(trim($_POST['email']));
    $newPassword = $_POST['signup-password'];
    $role = strtolower(trim($_SESSION['selected_role'] ?? ''));

    // Validation
    if(!$role){
        echo "<script>alert('No role selected. Please go back to select a role.'); window.location.href='../index.php';</script>";
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    if($user = $result->fetch_assoc()){
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if($stmt->execute()){
            echo "<script>
                    alert('Password reset successful!');
                    document.addEventListener('DOMContentLoaded', function () {
                        document.getElementById('forgot-password').reset();
                    });
                    window.location.href='../access.php';
                </script>";
        } else {
            echo "<script>alert('Something went wrong.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.history.back();</script>";
    }
}
?>