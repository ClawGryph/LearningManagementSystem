<?php
include '../db.php';
session_start();

$courseID = $_SESSION['courseID'] ?? null;

if (!$courseID) {
    die("No course ID in session.");
}

// Fetch course name
$stmt = $conn->prepare("SELECT CONCAT(courseCode, ' - ', courseName) AS class_name FROM courses WHERE courseID = ?");
$stmt->bind_param("i", $courseID);
$stmt->execute();
$stmt->bind_result($courseName);
$stmt->fetch();
$stmt->close();

// Fetch instructor_courseID
$stmt = $conn->prepare("SELECT instructor_courseID FROM instructor_courses WHERE courseID = ?");
$stmt->bind_param("i", $courseID);
$stmt->execute();
$stmt->bind_result($instructorCourseID);
$stmt->fetch();
$stmt->close();

// Default task counters
$taskCounts = [
    'completed' => 0,
    'incomplete' => 0,
    'overdue' => 0,
    'total' => 0
];

// Fetch task counts
$stmt = $conn->prepare("
    SELECT 
        sa.status,
        COUNT(*) as count
    FROM student_assessments sa
    JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
    WHERE aa.instructor_courseID = ?
    GROUP BY sa.status
");
$stmt->bind_param("i", $instructorCourseID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $status = $row['status'];
    $count = (int)$row['count'];
    $taskCounts['total'] += $count;

    if ($status === 'submitted' || $status === 'graded') {
        $taskCounts['completed'] += $count;
    } elseif ($status === 'assigned' || $status === 'in_progress') {
        $taskCounts['incomplete'] += $count;
    } elseif ($status === 'late') {
        $taskCounts['overdue'] += $count;
    }
}

$stmt->close();
?>