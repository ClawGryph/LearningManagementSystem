<?php
session_start();
include '../db.php';

require '../action/PHPMailer-master/src/PHPMailer.php';
require '../action/PHPMailer-master/src/SMTP.php';
require '../action/PHPMailer-master/src/Exception.php';
require 'emailConfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$query = $conn->prepare("SELECT email, appPassword FROM confidential");
$query->execute();
$query->bind_result($supportEmail, $supportPassword);
$query->fetch();
$query->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $hashedEmail = hashEmail($email);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $stmt->bind_param("s", $hashedEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    }

    // Generate verification code
    $length = 6; // desired length
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $verificationCode = '';
    for ($i = 0; $i < $length; $i++) {
        $verificationCode .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Store in session
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['signup_data'] = [
        'role' => $_POST['role'],
        'firstname' => $_POST['firstname'],
        'lastName' => $_POST['lastName'],
        'email' => $hashedEmail,
        'password' => $_POST['signup-password']
    ];

    // Send email (basic example, better use PHPMailer/SMTP)
    $mail = new PHPMailer(true);
    try {
        //Server settings
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

        //Recipients
        $mail->setFrom($supportEmail, 'LMS Support');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body    = "Your verification code is: <b>$verificationCode</b>";

        $mail->send();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Could not send email. Mailer Error: {$mail->ErrorInfo}"]);
        exit;
    }

    echo json_encode(["status" => "success", "message" => "Code sent"]);
}
?>