<?php
include '../db.php';
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['courseID']) || empty($_SESSION['user_id']) || empty($_SESSION['classID'])) {
    echo json_encode(['error' => 'No course ID and/or instructor ID in session.']);
    exit;
}
$courseID = $_SESSION['courseID'];
$instructorID = $_SESSION['user_id'];
$classID = $_SESSION['classID'];

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
    $progressBreakdown = [
        'quiz' => ['completed' => 0, 'incomplete' => 0, 'overdue' => 0, 'total' => 0],
        'assignment' => ['completed' => 0, 'incomplete' => 0, 'overdue' => 0, 'total' => 0],
        'activity' => ['completed' => 0, 'incomplete' => 0, 'overdue' => 0, 'total' => 0]
    ];

    $stmt = $conn->prepare("
        SELECT sa.status, aa.assessment_type, COUNT(*) as count
        FROM student_assessments sa
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        JOIN instructor_student_load isl 
            ON isl.studentID = sa.student_id 
        JOIN instructor_courses ic ON aa.instructor_courseID = ic.instructor_courseID
        WHERE aa.instructor_courseID = ?
        AND isl.instructor_courseID = aa.instructor_courseID
        AND isl.status = 'approved'
        AND ic.classID = ?
        GROUP BY aa.assessment_type, sa.status
    ");
    $stmt->bind_param("ii", $instructorCourseID, $classID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $type = $row['assessment_type'];
        $count = (int)$row['count'];
        $status = $row['status'];

        if (in_array($status, ['submitted','graded'])) {
            $progressBreakdown[$type]['completed'] += $count;
        } elseif (in_array($status, ['assigned','in_progress'])) {
            $progressBreakdown[$type]['incomplete'] += $count;
        } elseif ($status === 'late') {
            // ✅ Still a submission
            $progressBreakdown[$type]['completed'] += $count;

            // 🔖 Add a "late" flag for UI
            $progressBreakdown[$type]['late'] = 
                ($progressBreakdown[$type]['late'] ?? 0) + $count;
        }
    }

    // Student scores
    // Student scores grouped by type
    $studentScoresByType = [
        'all' => [],
        'quiz' => [],
        'activity' => [],
        'assignment' => []
    ];

    $types = ['quiz', 'activity', 'assignment'];

    foreach ($types as $type) {
        $stmt = $conn->prepare("
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS studentName, AVG(sa.score) AS averageScore
            FROM student_assessments sa
            JOIN users u ON sa.student_id = u.userID
            JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
            JOIN instructor_student_load isl ON isl.studentID = sa.student_id 
            JOIN instructor_courses ic ON aa.instructor_courseID = ic.instructor_courseID
            WHERE aa.instructor_courseID = ?
            AND aa.assessment_type = ?
            AND sa.status IN ('submitted', 'graded') 
            AND sa.score IS NOT NULL
            AND isl.instructor_courseID = aa.instructor_courseID
            AND isl.status = 'approved'
            AND ic.classID = ?
            GROUP BY u.userID
            ORDER BY studentName ASC
        ");
        $stmt->bind_param("isi", $instructorCourseID, $type, $classID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $studentScoresByType[$type][] = [
                'name' => $row['studentName'],
                'score' => round($row['averageScore'], 2)
            ];
        }
        $stmt->close();
    }

    // For 'all' category (merged scores)
    $mergedScores = [];
    foreach (['quiz', 'activity', 'assignment'] as $type) {
        foreach ($studentScoresByType[$type] as $entry) {
            $name = $entry['name'];
            $score = $entry['score'];

            if (!isset($mergedScores[$name])) {
                $mergedScores[$name] = ['scoreSum' => 0, 'count' => 0];
            }

            $mergedScores[$name]['scoreSum'] += $score;
            $mergedScores[$name]['count'] += 1;
        }
    }

    foreach ($mergedScores as $name => $data) {
        $studentScoresByType['all'][] = [
            'name' => $name,
            'score' => round($data['scoreSum'] / $data['count'], 2)
        ];
    }

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
            COALESCE(q.title, a.title, asg.title, 'Unknown') AS title,
            COALESCE(q.max_score, a.max_score, asg.max_score, NULL) AS max_score
        FROM student_assessments sa
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        JOIN instructor_courses ic ON aa.instructor_courseID = ic.instructor_courseID
        LEFT JOIN quizzes q ON aa.assessment_type = 'quiz' AND q.quizID = aa.assessment_refID
        LEFT JOIN programming_activity a ON aa.assessment_type = 'activity' AND a.activityID = aa.assessment_refID
        LEFT JOIN assignment asg ON aa.assessment_type = 'assignment' AND asg.assignmentID = aa.assessment_refID
        WHERE aa.instructor_courseID = ?
        AND ic.classID = ?
        GROUP BY aa.assessment_type, aa.assessment_refID
    ");

    $stmt->bind_param("ii", $instructorCourseID, $classID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $type = $row['assessment_type']; // 'quiz' or 'activity'
        $title = $row['title'] ?? 'Untitled';
        $count = (int)$row['count'];
        $tabs_open = (int) $row['tabs_open'];
        $max_score = (int) $row['max_score'];

        $taskBreakdown[$type][] = [
            'title' => $title,
            'count' => $count,
            'tabs_open' => $tabs_open,
            'max_score' => $max_score
        ];
    }
    $stmt->close();


    // Per-student task status
    $studentTaskStatus = [];

    $stmt = $conn->prepare("
        SELECT 
            CONCAT(u.firstName, ' ', u.lastName) AS studentName,
            u.profileImage,
            sa.status,
            sa.score,
            aa.assessment_type,
            aa.assessment_refID,
            COALESCE(q.title, a.title, asg.title, 'Unknown') AS title,
            COALESCE(q.max_score, a.max_score, asg.max_score, NULL) AS max_score
        FROM student_assessments sa
        JOIN users u ON sa.student_id = u.userID
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        JOIN instructor_student_load isl ON isl.studentID = sa.student_id 
        JOIN instructor_courses ic ON aa.instructor_courseID = ic.instructor_courseID
        LEFT JOIN quizzes q ON aa.assessment_type = 'quiz' AND q.quizID = aa.assessment_refID
        LEFT JOIN programming_activity a ON aa.assessment_type = 'activity' AND a.activityID = aa.assessment_refID
        LEFT JOIN assignment asg ON aa.assessment_type = 'assignment' AND asg.assignmentID = aa.assessment_refID
        WHERE aa.instructor_courseID = ?
        AND ic.classID = ?
        AND isl.instructor_courseID = aa.instructor_courseID
        AND isl.status = 'approved'
    ");

    $stmt->bind_param("ii", $instructorCourseID, $classID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $studentTaskStatus[] = [
            'studentName' => $row['studentName'],
            'profileImage' => $row['profileImage'],
            'status' => $row['status'],
            'score' => is_null($row['score']) ? '-' : round($row['score'], 2),
            'max_score' => $row['max_score'],
            'type' => $row['assessment_type'],
            'title' => $row['title']
        ];
    }

    $stmt->close();


    echo json_encode([
        'success' => true,
        'courseName' => $courseName,
        'progressBreakdown' => $progressBreakdown,
        'studentScoresByType' => $studentScoresByType,
        'taskBreakdown' => $taskBreakdown,
        'studentTaskStatus' => $studentTaskStatus
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
