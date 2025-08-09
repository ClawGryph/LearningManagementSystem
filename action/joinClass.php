<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classCode = trim($_POST['classCode']);
    $studentID = $_SESSION['user_id'];

    // 1. Check if class code exists
    $stmt = $conn->prepare("SELECT instructor_courseID FROM instructor_courses WHERE code = ?");
    $stmt->bind_param("s", $classCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Invalid class code."]);
        exit;
    }

    $classData = $result->fetch_assoc();
    $instructor_courseID = $classData['instructor_courseID'];

    // 2. Check if student already enrolled
    $checkEnroll = $conn->prepare("SELECT * FROM instructor_student_load WHERE instructor_courseID = ? AND studentID = ?");
    $checkEnroll->bind_param("ii", $instructor_courseID, $studentID);
    $checkEnroll->execute();
    if ($checkEnroll->get_result()->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "You are already enrolled in this class."]);
        exit;
    }

    // 3. Enroll the student
    $enroll = $conn->prepare("INSERT INTO instructor_student_load (instructor_courseID, studentID) VALUES (?, ?)");
    $enroll->bind_param("ii", $instructor_courseID, $studentID);
    $enroll->execute();

    // 4. Assign all existing assessments in that class
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

    echo json_encode(["success" => true, "message" => "Successfully joined the class and assessments assigned."]);
}
?>