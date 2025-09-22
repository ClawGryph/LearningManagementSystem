<?php
session_start();
include '../db.php';

require '../action/PHPMailer-master/src/PHPMailer.php';
require '../action/PHPMailer-master/src/SMTP.php';
require '../action/PHPMailer-master/src/Exception.php';
require 'emailConfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_POST['email'])) {
    echo json_encode(['exists' => false, 'error' => 'No email provided']);
    exit;
}

$email = strtolower(trim($_POST['email']));
$hashedEmail = hashEmail($email);

// Check if email exists in database
$stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
$stmt->bind_param("s", $hashedEmail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['exists' => false, 'error' => 'Email not found']);
    exit;
}

// Email exists → generate verification code
$length = 6; // length of code
$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$verificationCode = '';
for ($i = 0; $i < $length; $i++) {
    $verificationCode .= $characters[rand(0, strlen($characters) - 1)];
}

// Store in session
$_SESSION['verification_code'] = $verificationCode;
$_SESSION['reset_email'] = $email;

// Fetch support email credentials
$query = $conn->prepare("SELECT email, appPassword FROM confidential");
$query->execute();
$query->bind_result($supportEmail, $supportPassword);
$query->fetch();
$query->close();

// Send verification email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $supportEmail;
    $mail->Password   = $supportPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom($supportEmail, 'LMS Support');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body    = "Your verification code is: <b>$verificationCode</b>";

    $mail->send();

    echo json_encode(['exists' => true, 'status' => 'success', 'message' => 'Verification code sent']);
} catch (Exception $e) {
    echo json_encode(['exists' => true, 'status' => 'error', 'message' => 'Could not send email. Error: ' . $mail->ErrorInfo]);
}

$stmt->close();
$conn->close();
?>