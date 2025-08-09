<?php
include '../db.php';
session_start();

$studentID = $_SESSION['user_id'] ?? null;
$instructorCourseID = $_SESSION['instructor_courseID'] ?? null;

if (!$studentID || !$instructorCourseID) {
    echo json_encode(["quiz" => 0, "assignment" => 0, "activity" => 0]);
    exit;
}

$sql = "
SELECT 
    aa.assessment_type,
    ROUND((SUM(CASE WHEN sa.status IN ('submitted', 'graded') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 0) AS percent
FROM student_assessments sa
JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
WHERE sa.student_id = ? 
  AND aa.instructor_courseID = ?
GROUP BY aa.assessment_type
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentID, $instructorCourseID);
$stmt->execute();
$result = $stmt->get_result();

$progress = [
    "quiz" => 0,
    "assignment" => 0,
    "activity" => 0
];

while ($row = $result->fetch_assoc()) {
    $type = strtolower($row['assessment_type']);
    if (isset($progress[$type])) {
        $progress[$type] = (int)$row['percent'];
    }
}

echo json_encode($progress);
?>