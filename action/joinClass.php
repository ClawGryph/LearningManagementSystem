<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classCode = trim($_POST['classCode']);
    $studentID = $_SESSION['user_id'];

    // 1. Check if class code exists
    $stmt = $conn->prepare("SELECT instructor_courseID, courseID FROM instructor_courses WHERE code = ?");
    $stmt->bind_param("s", $classCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Invalid class code."]);
        exit;
    }

    $classData = $result->fetch_assoc();
    $instructor_courseID = $classData['instructor_courseID'];
    $courseID = $classData['courseID'];

    // 2. Check if student already enrolled in this subject (any class under the same courseID)
    $checkEnroll = $conn->prepare("
        SELECT isl.* 
        FROM instructor_student_load isl
        JOIN instructor_courses ic ON isl.instructor_courseID = ic.instructor_courseID
        WHERE ic.courseID = ? AND isl.studentID = ?
    ");
    $checkEnroll->bind_param("ii", $courseID, $studentID);
    $checkEnroll->execute();
    $existing = $checkEnroll->get_result()->fetch_assoc();

    if ($existing) {
        if ($existing['status'] === 'pending') {
            echo json_encode(["success" => false, "message" => "Your request is still pending approval."]);
        } elseif ($existing['status'] === 'approved') {
            echo json_encode(["success" => false, "message" => "You are already enrolled in this class."]);
        } elseif ($existing['status'] === 'rejected') {
            echo json_encode(["success" => false, "message" => "Your request to join this class was rejected."]);
        }
        exit;
    }

    // 3. Insert student with status = 'pending'
    $enroll = $conn->prepare("INSERT INTO instructor_student_load (instructor_courseID, studentID) VALUES (?, ?)");
    $enroll->bind_param("ii", $instructor_courseID, $studentID);

    if (!$enroll->execute()) {
        echo json_encode(["success" => false, "message" => "Failed to send join request. Please try again."]);
        exit;
    }

    echo json_encode(["success" => true, "message" => "Join request sent! Waiting for instructor approval."]);


    // 4. Assign all existing assessments in that class
    // $assessments = $conn->prepare("SELECT assessment_authorID FROM assessment_author WHERE instructor_courseID = ?");
    // $assessments->bind_param("i", $instructor_courseID);
    // $assessments->execute();
    // $assessmentsResult = $assessments->get_result();

    // $assignStmt = $conn->prepare("INSERT INTO student_assessments (student_id, assessment_authorID, status) VALUES (?, ?, 'assigned')");
    // while ($row = $assessmentsResult->fetch_assoc()) {
    //     $assessment_authorID = $row['assessment_authorID'];
    //     $assignStmt->bind_param("ii", $studentID, $assessment_authorID);
    //     $assignStmt->execute();
    // }
    // $assignStmt->close();

    // message
}
?>