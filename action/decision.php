<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestID = $_POST['requestID'];
    $action = $_POST['action'];

    // VALIDATION
    if (!in_array($action, ['approved', 'rejected'])) {
        echo "Invalid action";
        exit;
    }

    // 1. Update status
    $stmt = $conn->prepare("UPDATE instructor_student_load SET status = ?, decision_date = NOW() WHERE instructor_student_loadID = ?");
    $stmt->bind_param("si", $action, $requestID);

    if (!$stmt->execute()) {
        echo "Failed to update request.";
        exit;
    }
    $stmt->close();

    // 2. If approved → assign assessments
    if ($action === 'approved') {
        // Get studentID + instructor_courseID
        $getDetails = $conn->prepare("SELECT studentID, instructor_courseID FROM instructor_student_load WHERE instructor_student_loadID = ?");
        $getDetails->bind_param("i", $requestID);
        $getDetails->execute();
        $detailsResult = $getDetails->get_result();
        $details = $detailsResult->fetch_assoc();
        $getDetails->close();

        if ($details) {
            $studentID = $details['studentID'];
            $instructor_courseID = $details['instructor_courseID'];

            // Get all assessments for this course
            $assessments = $conn->prepare("SELECT assessment_authorID FROM assessment_author WHERE instructor_courseID = ?");
            $assessments->bind_param("i", $instructor_courseID);
            $assessments->execute();
            $assessmentsResult = $assessments->get_result();

            $assignStmt = $conn->prepare("INSERT INTO student_assessments (student_id, assessment_authorID, status) VALUES (?, ?, 'assigned')");
            while ($row = $assessmentsResult->fetch_assoc()) {
                $assessment_authorID = $row['assessment_authorID'];
                $assignStmt->bind_param("ii", $studentID, $assessment_authorID);
                $assignStmt->execute();
            }
            $assignStmt->close();
            $assessments->close();
        }
    }

    echo "Request successfully " . $action . ".";
}
?>
