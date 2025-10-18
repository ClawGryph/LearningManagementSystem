<?php
include '../db.php';
header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_POST['notifications'])) {
    foreach ($_POST['notifications'] as $notif) {
        if (strpos($notif, 'lm_') === 0) {
            $lmID = (int) str_replace('lm_', '', $notif);
            $conn->query("UPDATE learningmaterials_author SET instructor_read = 1 WHERE lmID = $lmID");
        } elseif (strpos($notif, 'join_') === 0) {
            $requestID = (int) str_replace('join_', '', $notif);
            $conn->query("UPDATE instructor_student_load SET is_read = 1 WHERE instructor_student_loadID = $requestID");
        }
    }

    $response['success'] = true;
}

// Return JSON response (no redirect)
echo json_encode($response);
?>
