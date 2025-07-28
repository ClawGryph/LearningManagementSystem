<?php
include '../db.php';
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['courseID']) || empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No course ID and/or instructor ID in session.']);
    exit;
}
$courseID = $_SESSION['courseID'];
$instructorID = $_SESSION['user_id'];

// Fetch course name
$courseName = '';
$stmt = $conn->prepare("SELECT CONCAT(courseCode, ' - ', courseName) AS class_name FROM courses WHERE courseID = ?");
$stmt->bind_param("i", $courseID);
$stmt->execute();
$stmt->bind_result($courseName);
$stmt->fetch();
$stmt->close();

try {
    $stmt = $conn->prepare("SELECT instructor_courseID FROM instructor_courses WHERE instructorID = ? AND courseID = ?");
    $stmt->bind_param("ii", $instructorID, $courseID);
    $stmt->execute();
    $stmt->bind_result($instructorCourseID);
    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'error' => 'No instructor_courseID found for this instructor and course.']);
        exit;
    }

    if (!$instructorCourseID) {
        throw new Exception("No instructor_courseID found.");
    }

    // Task counts
    $taskCounts = [
        'completed' => 0,
        'incomplete' => 0,
        'overdue' => 0,
        'total' => 0
    ];

    $stmt = $conn->prepare("
        SELECT sa.status, COUNT(*) as count
        FROM student_assessments sa
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        WHERE aa.instructor_courseID = ?
        GROUP BY sa.status
    ");
    $stmt->bind_param("i", $instructorCourseID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $count = (int)$row['count'];
        $status = $row['status'];
        $taskCounts['total'] += $count;

        if (in_array($status, ['submitted', 'graded'])) {
            $taskCounts['completed'] += $count;
        } elseif (in_array($status, ['assigned', 'in_progress'])) {
            $taskCounts['incomplete'] += $count;
        } elseif ($status === 'late') {
            $taskCounts['overdue'] += $count;
        }
    }
    $stmt->close();

    // Student scores
    $studentScores = [];
    $stmt = $conn->prepare("
        SELECT CONCAT(u.firstName, ' ', u.lastName) AS studentName, AVG(sa.score) AS averageScore
        FROM student_assessments sa
        JOIN users u ON sa.student_id = u.userID
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        WHERE aa.instructor_courseID = ?
        AND sa.status IN ('submitted', 'graded') AND sa.score IS NOT NULL
        GROUP BY u.userID
        ORDER BY studentName ASC
    ");
    $stmt->bind_param("i", $instructorCourseID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $studentScores[] = [
            'name' => $row['studentName'],
            'score' => round($row['averageScore'], 2)
        ];
    }
    $stmt->close();

    //QUIZ AND ACTIVITIES
    $taskBreakdown = [
        'quiz' => [],
        'activity' => [],
        'assignment' => []
    ];

    // Fetch quiz and activity names + counts
    $stmt = $conn->prepare("
        SELECT 
            aa.assessment_type,
            aa.assessment_refID,
            COUNT(sa.assessment_authorID) AS count,
            SUM(sa.tabs_open) AS tabs_open,
            CASE 
                WHEN aa.assessment_type = 'quiz' THEN (SELECT q.title FROM quizzes q WHERE q.quizID = aa.assessment_refID)
                WHEN aa.assessment_type = 'activity' THEN (SELECT a.title FROM programming_activity a WHERE a.activityID = aa.assessment_refID)
                WHEN aa.assessment_type = 'assignment' THEN (SELECT asg.title FROM assignment asg WHERE asg.assignmentID = aa.assessment_refID)
                ELSE 'Unknown'
            END AS title
        FROM student_assessments sa
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        WHERE aa.instructor_courseID = ?
        GROUP BY aa.assessment_type, aa.assessment_refID
    ");

    $stmt->bind_param("i", $instructorCourseID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $type = $row['assessment_type']; // 'quiz' or 'activity'
        $title = $row['title'] ?? 'Untitled';
        $count = (int)$row['count'];
        $tabs_open = (int) $row['tabs_open'];

        $taskBreakdown[$type][] = [
            'title' => $title,
            'count' => $count,
            'tabs_open' => $tabs_open
        ];
    }
    $stmt->close();


    echo json_encode([
        'success' => true,
        'courseName' => $courseName,
        'taskCounts' => $taskCounts,
        'studentScores' => $studentScores,
        'taskBreakdown' => $taskBreakdown
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
