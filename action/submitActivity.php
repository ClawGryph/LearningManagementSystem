<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        exit;
    }

    $student_id = $_SESSION['user_id'];
    $activityID = intval($_POST['activityID']); // from form
    $code_submission = $_POST['code_submission'];
    $tabSwitchCount = isset($_POST['tabSwitchCount']) ? intval($_POST['tabSwitchCount']) : 0;

    if (empty($code_submission)) {
        echo json_encode(["status" => "error", "message" => "Code cannot be empty"]);
        exit;
    }

    // 🔎 Find the correct assessment_authorID for this student's assigned activity
    $sql = "SELECT assessment_authorID 
            FROM assessment_author 
            WHERE assessment_refID = ? AND assessment_type = 'activity' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $activityID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Assessment not found for this activity"]);
        exit;
    }

    $row = $result->fetch_assoc();
    $assessment_authorID = $row['assessment_authorID'];

    // 💾 Insert submission
    $sql = "INSERT INTO student_submissions (student_id, assessment_authorID, code_submission, submitted_at) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $assessment_authorID, $code_submission);

    if ($stmt->execute()) {
        // ✅ Update status in student_assessments
        $update = $conn->prepare("UPDATE student_assessments 
                                  SET status = 'submitted', tabs_open = ?, submission_date = NOW() 
                                  WHERE student_id = ?  AND assessment_authorID = ?");
        $update->bind_param("iii", $tabSwitchCount, $student_id, $assessment_authorID);
        $update->execute();

        echo json_encode(["status" => "success", "message" => "Code submitted and status updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save code"]);
    }
}
?>