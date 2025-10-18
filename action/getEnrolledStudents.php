<?php
include '../db.php';

if (isset($_POST['instructor_courseID'])) {
    $courseId = intval($_POST['instructor_courseID']);

    $stmt = $conn->prepare("
        SELECT 
            s.userID AS student_id,
            CONCAT(s.firstName, ' ', s.lastName) AS Student_Name
        FROM 
            instructor_student_load isl
        JOIN 
            users s ON isl.studentID = s.userID
        WHERE 
            isl.instructor_courseID = ? 
            AND isl.status = 'approved'
    ");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='page-header'>";
            echo "<h2>Enrolled Students:</h2>";
            echo "<i class='fa-solid fa-xmark' id='closeBtn'></i>";
        echo "</div>";
        echo "<input type='hidden' name='instructor_courseID' value='{$courseId}'>";
        echo "<ol class='enrolees-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "
                <li class='row' data-student-id='{$row['student_id']}'>
                    " . htmlspecialchars($row['Student_Name']) . "
                    <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'>
                        <i class='fa-solid fa-trash'></i>
                    </button>
                </li>
            ";
        }
        echo "</ol>";
    } else {
        echo "<p>No students enrolled in this course.</p>";
    }

    $stmt->close();
}
?>