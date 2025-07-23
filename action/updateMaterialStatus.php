<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lmID = $_POST['lmID'];
    $action = $_POST['action']; // either 'approved' or 'rejected'

    // Validate action
    if (!in_array($action, ['approved', 'rejected'])) {
        echo 'Invalid action';
        exit;
    }

    $stmt = $conn->prepare("UPDATE course_learningmaterials clm
        JOIN learningmaterials_author lma ON lma.course_lmID = clm.course_lmID
        SET clm.status = ?, lma.is_read = 1
        WHERE lma.lmID = ?");
    $stmt->bind_param("si", $action, $lmID);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
