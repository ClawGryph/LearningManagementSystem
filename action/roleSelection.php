<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'select';
    if ($role === 'admin') {
        header('Location: ../adminAccess.php');
        exit;
    } elseif ($role === 'instructor' || $role === 'student') {
        header('Location: ../access.php');
        exit;
    }
    // Optional: Show a message if "Select Role" is chosen
    echo "<script>alert('Please select a valid role.'); window.history.back();</script>";
    exit;
}
?>