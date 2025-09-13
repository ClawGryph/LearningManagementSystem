<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notifications'])) {
    foreach ($_POST['notifications'] as $notif) {
        // Split into [type, id]
        list($type, $id) = explode(':', $notif);

        if ($type === "assessment") {
            $stmt = $conn->prepare("UPDATE student_assessments SET is_read = 1 WHERE record_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } 
        elseif ($type === "material") {
            $stmt = $conn->prepare("UPDATE learningmaterials_author SET student_read = 1 WHERE lmID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } 
        elseif ($type === "join") {
            $stmt = $conn->prepare("UPDATE instructor_student_load SET student_read = 1 WHERE instructor_student_loadID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }

    // Redirect back after marking read
    header('Location: ../student/student-landingpage.php');
    exit;
}
?>