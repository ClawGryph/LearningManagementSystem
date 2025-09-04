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

    // 🔎 get assessment_authorID for this student's assigned activity
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

    // Get deadline from programming_activity
    $sql_deadline = "SELECT expected_output, language, max_score, deadline FROM programming_activity WHERE activityID = ? LIMIT 1";
    $stmt_deadline = $conn->prepare($sql_deadline);
    $stmt_deadline->bind_param("i", $activityID);
    $stmt_deadline->execute();
    $res_deadline = $stmt_deadline->get_result();

    if ($res_deadline->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Activity deadline not found"]);
        exit;
    }

    $row_deadline = $res_deadline->fetch_assoc();
    $deadline = $row_deadline['deadline'];
    $language = $row_deadline['language'];
    $expected_output = trim($row_deadline['expected_output']);
    $max_score = $row_deadline['max_score'];

    // Map language to Judge0 ID
    function getLanguageID($language) {
        switch ($language) {
            case "c": return 50;
            case "java": return 62;
            default: return 50; // fallback
        }
    }
    $lang_id = getLanguageID($language);

     // 🔹 Call Judge0 API
    $payload = json_encode([
        "source_code" => $code_submission,
        "language_id" => $lang_id,
    ]);

    $ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/projects/learningmanagementsystem/action/proxy.php"); // adjust path to your proxy.php
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $actual_output = $result['stdout'] ?? $result['stderr'] ?? "";

    // 🔹 Normalize text (ignore case, spaces, newlines)
    function normalizeText($text) {
        $text = strtolower($text);                   // ignore case
        $text = preg_replace('/\s+/', ' ', $text);   // collapse whitespace/newlines
        return trim($text);
    }

     // Split expected vs actual into lines
    $expected_lines = array_filter(array_map('trim', explode("\n", $expected_output)));
    $actual_lines   = array_filter(array_map('trim', explode("\n", $actual_output)));

    $total = count($expected_lines);
    $correct_count = 0;

    foreach ($expected_lines as $i => $expLine) {
        $expNorm = normalizeText($expLine);
        $actNorm = isset($actual_lines[$i]) ? normalizeText($actual_lines[$i]) : "";

        if ($expNorm === $actNorm) {
            $correct_count++;
        }
    }

    $score = ($total > 0) ? round(($correct_count / $total) * $max_score, 2) : 0;
    $is_correct = ($correct_count === $total) ? 1 : 0;

    // Insert submission
    $sql = "INSERT INTO student_submissions (student_id, assessment_authorID, code_submission, submitted_at) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $assessment_authorID, $code_submission);

     // If insert successful

    if ($stmt->execute()) {

        $currentTime = date("Y-m-d H:i:s");
        $status = (strtotime($currentTime) > strtotime($deadline)) ? "late" : "submitted";

        // ✅ Update status in student_assessments
        $update = $conn->prepare("UPDATE student_assessments 
                                  SET status = ?, tabs_open = ?, submission_date = NOW(), score = ? 
                                  WHERE student_id = ?  AND assessment_authorID = ?");
        $update->bind_param("siiii", $status, $tabSwitchCount, $score, $student_id, $assessment_authorID);
        $update->execute();

        echo json_encode(["status" => "success", "message" => "Code submitted and status updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save code"]);
    }
}
?>