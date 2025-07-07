<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['eMail']));
    $password = $_POST['password'];
    $role = strtolower(trim($_SESSION['selected_role'] ?? ''));

    // Validation
    if (!$role) {
        echo "<script>alert('No role selected. Please go back to select a role.'); window.location.href='../index.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../admin/admin-landingpage.php");
                    break;
                case 'instructor':
                    header("Location: ../instructor/instructor-landingpage.php");
                    break;
                case 'student':
                    header("Location: ../student/student-landingpage.php");
                    break;
            }
            exit;
        }
    }

    $_SESSION['signin_error'] = "Incorrect email or password.";
    if($role === 'admin') {
        header("Location: ../adminAccess.php");
    } else {
        header("Location: ../access.php");
    }
    exit;
}
?>
