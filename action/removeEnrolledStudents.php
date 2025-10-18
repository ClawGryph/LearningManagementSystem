<?php
include '../db.php';

if (isset($_POST['student_id'], $_POST['instructor_courseID'])) {
    $studentId = intval($_POST['student_id']);
    $courseId = intval($_POST['instructor_courseID']);

    // Get assessment authors
    $stmt = $conn->prepare("
        SELECT assessment_authorID 
        FROM assessment_author
        WHERE instructor_courseID = ?
    ");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $assessmentAuthorIds = [];
    while ($row = $result->fetch_assoc()) {
        $assessmentAuthorIds[] = $row['assessment_authorID'];
    }
    $stmt->close();

    // If no authors found, just remove the student from the course
    if (empty($assessmentAuthorIds)) {
        $stmt = $conn->prepare("
            DELETE FROM instructor_student_load
            WHERE studentID = ? AND instructor_courseID = ?
        ");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        $stmt->close();

        echo "success";
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($assessmentAuthorIds), '?'));

    $conn->begin_transaction();

    try {
        // Build parameter types dynamically
        $types = str_repeat('i', count($assessmentAuthorIds) + 1);
        $params = array_merge([$types, $studentId], $assessmentAuthorIds);

        // Delete from student_assessments
        $sql = "
            DELETE FROM student_assessments
            WHERE student_id = ?
            AND assessment_authorID IN ($placeholders)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $stmt->close();

        // Delete from student_submissions
        $sql = "
            DELETE FROM student_submissions 
            WHERE student_id = ?
            AND assessment_authorID IN ($placeholders)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $stmt->close();

        // Delete from instructor_student_load
        $stmt = $conn->prepare("
            DELETE FROM instructor_student_load
            WHERE studentID = ? AND instructor_courseID = ?
        ");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo "success";

    } catch (Exception $e) {
        $conn->rollback();
        echo "error: " . $e->getMessage() . " | MySQL: " . $conn->error;
    }
}
?>