<?php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'No code provided']);
    exit;
}

$enteredCode = trim($_POST['code']);

// Compare with session
if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] === $enteredCode) {
    echo json_encode(['status' => 'success', 'message' => 'Code verified']);
    unset($_SESSION['verification_code']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
}

?>