<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['eMail']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['userID'];
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
    header("Location: ../index.php");
    exit;
}
?>
