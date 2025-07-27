<?php
include '../db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $instructor_courseID = $_POST['instructorCourseID'];
    $activityID = $_POST['activityID'];
    $activityTime = $_POST['activityTime'];
    $assessmentType = 'activity';

    $checkStmt = $conn->prepare("SELECT aa.instructor_courseID, aa.assessment_refID, aa.assessment_type FROM assessment_author aa JOIN programming_activity pa ON aa.assessment_refID = pa.activityID WHERE instructor_courseID = ? AND assessment_refID = ? AND assessment_type = ?");
    $checkStmt->bind_param("iis", $instructor_courseID, $activityID, $assessmentType);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Quiz already added to class
        echo "<script>alert('This activity is already added to the selected class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        // Step 1: Insert into assessment_author
        $stmt = $conn->prepare("
            INSERT INTO assessment_author (instructor_courseID, assessment_type, assessment_refID, assessment_time) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isii", $instructor_courseID, $assessmentType, $activityID, $activityTime);

        if ($stmt->execute()) {
            $assessmentAuthorID = $conn->insert_id;

            // Step 2: Insert into assessments
            $stmt2 = $conn->prepare("
                INSERT INTO assessments (assessment_authorID, type) 
                VALUES (?, ?)
            ");
            $stmt2->bind_param("is", $assessmentAuthorID, $assessmentType );
            if ($stmt2->execute()) {
                echo "<script>alert('Successfully added to class.'); window.location.href='../instructor/instructor-landingpage.php';</script>";
            } else {
                echo "<script>alert('Failed to add to assessments: " . htmlspecialchars($stmt2->error) . "');</script>";
            }

            $stmt2->close();
        } else {
            echo "<script>alert('Failed to add the activity to class: " . htmlspecialchars($stmt->error) . "');</script>";
        }

        $stmt->close();
    }

    $checkStmt->close();
}
?>