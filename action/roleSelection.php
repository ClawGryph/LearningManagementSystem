<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = strtolower(trim($_POST['role'] ?? 'select'));

    if ($role === 'admin') {
        $_SESSION['selected_role'] = $role;

        header('Location: ../adminAccess.php');
        exit;
    } elseif ($role === 'instructor' || $role === 'student') {
        $_SESSION['selected_role'] = $role;

        header('Location: ../access.php');
        exit;
    }
    // Optional: Show a message if "Select Role" is chosen
    echo "<script>alert('Please select a valid role.'); window.history.back();</script>";
    exit;
}
?>