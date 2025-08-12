<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_file'])) {
    $studentID = $_SESSION['user_id'];
    $assignmentID = $_POST['assignmentID'];

    // Find assessment_authorID and deadline for this assignment
    $sql = "SELECT aa.assessment_authorID, a.deadline 
            FROM assessment_author aa
            JOIN assignment a ON a.assignmentID = aa.assessment_refID
            WHERE a.assignmentID = ? AND aa.assessment_type = 'assignment'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $assignmentID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Invalid assignment.");
    }

    $assessment_authorID = $row['assessment_authorID'];
    $deadline = $row['deadline'];

    // Determine status based on deadline
    $currentTime = date('Y-m-d H:i:s');
    $status = ($currentTime > $deadline) ? 'late' : 'submitted';

    // File upload path
    $uploadDir = '../uploads/submissions/';
    $fileName = time() . '_' . basename($_FILES['assignment_file']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $targetFile)) {
        // Insert into student_submissions table
        $sqlInsert = "INSERT INTO student_submissions 
            (student_id, assessment_authorID, file_path, submitted_at) 
            VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param("iis", $studentID, $assessment_authorID, $targetFile);
        $stmt->execute();

        // Update status in student_assessments table
        $updateSql = "UPDATE student_assessments 
                      SET status = ?, submission_date = NOW() 
                      WHERE student_id = ? AND assessment_authorID = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sii", $status, $studentID, $assessment_authorID);
        $stmt->execute();

        echo "<script>alert('Assignment submitted successfully!');</script>";
    } else {
        echo "<script>alert('File upload failed. Please try again.');</script>";
    }
}
?>