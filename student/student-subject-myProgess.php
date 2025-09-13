<?php
session_start();
include '../db.php';

$student_id = $_SESSION['user_id'];
$instructor_course_id = $_SESSION['instructor_courseID'];

$sql = "
    SELECT 
        aa.assessment_type,
        CASE 
            WHEN aa.assessment_type = 'quiz' THEN q.title
            WHEN aa.assessment_type = 'assignment' THEN a.title
            WHEN aa.assessment_type = 'activity' THEN pa.title
            ELSE 'Unknown'
        END AS title,
        sa.status,
        sa.score
    FROM student_assessments sa
    INNER JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
    LEFT JOIN quizzes q ON aa.assessment_type = 'quiz' AND aa.assessment_refID = q.quizID
    LEFT JOIN assignment a ON aa.assessment_type = 'assignment' AND aa.assessment_refID = a.assignmentID
    LEFT JOIN programming_activity pa ON aa.assessment_type = 'activity' AND aa.assessment_refID = pa.activityID
    WHERE sa.student_id = ?
    AND aa.instructor_courseID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $instructor_course_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <h2>Progress</h2>
            <div class="progress-section">
                <div class="donut-chart">
                    <canvas id="quizChart"></canvas>
                    <p>Quiz</p>
                </div>
                <div class="donut-chart">
                    <canvas id="assignmentChart"></canvas>
                    <p>Assignment</p>
                </div>
                <div class="donut-chart">
                    <canvas id="activityChart"></canvas>
                    <p>Activity</p>
                </div>
            </div>

            <div class="table-container">
                <table class="table-content" id="studentTaskTable">
                    <thead>
                        <tr>
                            <th>Assessment Type</th>
                            <th>Assessment Title</th>
                            <th>Status</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['assessment_type']) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                    <td><?= $row['score'] !== null ? htmlspecialchars($row['score']) : '-' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td>No assessments found.</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>