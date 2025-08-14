<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $assignmentID = $_POST['assignmentID'];
    $assessmentType = 'assignment';

    $checkStmt = $conn->prepare("SELECT aa.instructor_courseID, aa.assessment_refID, aa.assessment_type FROM assessment_author aa WHERE instructor_courseID = ? AND assessment_refID = ? AND assessment_type = ?");
    $checkStmt->bind_param("iis", $instructor_courseID, $assignmentID, $assessmentType);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This assignment is already added to the selected class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        // Step 1: Insert into assessment_author
        $stmt = $conn->prepare("
            INSERT INTO assessment_author (instructor_courseID, assessment_type, assessment_refID, upload_date) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("isi", $instructor_courseID, $assessmentType, $assignmentID);

        if ($stmt->execute()) {
            // Step 2: Get the inserted assessment_authorID
            $assessment_authorID = $conn->insert_id;

            // Step 3: Get all students enrolled in this class
            $studentQuery = $conn->prepare("SELECT studentID FROM instructor_student_load WHERE instructor_courseID = ?");
            $studentQuery->bind_param("i", $instructor_courseID);
            $studentQuery->execute();
            $studentsResult = $studentQuery->get_result();

            if ($studentsResult->num_rows === 0) {
                echo "<script>
                    alert('Assignment uploaded, but no students are currently enrolled in this class. They will be assigned automatically once enrolled.');
                    window.location.href='../instructor/instructor-landingpage.php';
                </script>";
                $studentQuery->close();
                exit;
            } else {
                // Step 4: Insert for each student into student_assessments
                $insertStmt = $conn->prepare("
                    INSERT INTO student_assessments (student_id, assessment_authorID, status) 
                    VALUES (?, ?, 'assigned')
                ");

                while ($row = $studentsResult->fetch_assoc()) {
                    $studentID = $row['studentID'];
                    $insertStmt->bind_param("ii", $studentID, $assessment_authorID);
                    $insertStmt->execute();
                }

                $insertStmt->close();
                echo "<script>alert('Successfully added to class and assigned to students.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
            }

            $studentQuery->close();
        } else {
            echo "<script>alert('Failed to add the activity to class: " . htmlspecialchars($stmt->error) . "');</script>";
        }

        $stmt->close();
    }

    $checkStmt->close();
}
?>