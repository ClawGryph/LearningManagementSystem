<?php
include '../db.php';
header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notifications'])) {
    foreach ($_POST['notifications'] as $notif) {
        list($type, $id) = explode(':', $notif);

        if ($type === "assessment") {
            $stmt = $conn->prepare("UPDATE student_assessments SET is_read = 1 WHERE record_id = ?");
        } elseif ($type === "material") {
            $stmt = $conn->prepare("UPDATE learningmaterials_author SET student_read = 1 WHERE lmID = ?");
        } elseif ($type === "join") {
            $stmt = $conn->prepare("UPDATE instructor_student_load SET student_read = 1 WHERE instructor_student_loadID = ?");
        }

        if (isset($stmt)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $response['success'] = true;
}

echo json_encode($response);
?>