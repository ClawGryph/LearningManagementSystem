<?php
include '../db.php';
session_start();

$studentID = $_SESSION['user_id']; 
$instructor_courseID = $_SESSION['instructor_courseID']; 

$sql = "
SELECT pa.activityID, pa.title, aa.assessment_time, pa.deadline, sa.status 
FROM student_assessments sa 
JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID 
JOIN programming_activity pa ON aa.assessment_refID = pa.activityID 
WHERE sa.student_id = ?
AND aa.instructor_courseID = ?
AND aa.assessment_type = 'activity';
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentID, $instructor_courseID);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <h2>Activities</h2>

            <div class="table-container">
                <table class="table-content">
                    <thead>
                        <th>Title</th>
                        <th>Time Duration</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </thead>
                    <tbody class="table-body">
                        <!-- Fetch Activities given by instructor -->
                        <?php foreach ($activities as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['title']) ?></td>
                                <td><?= htmlspecialchars($a['assessment_time']) ?> mins</td>
                                <td><?= date("F j, Y g:i A", strtotime($a['deadline'])) ?></td>
                                <td>
                                    <?php if ($a['status'] === 'submitted' || $a['status'] === 'graded'): ?>
                                        <span class="statusGroup <?= strtolower(str_replace(' ', '-', htmlspecialchars($a['status']))) ?>">Completed</span>
                                    <?php elseif ($a['status'] === 'late'): ?>
                                        <span class="statusGroup <?= strtolower(str_replace(' ', '-', htmlspecialchars($a['status']))) ?>">Late</span>
                                    <?php else: ?>
                                        <a href="student-subject-takeActivity.php?activityID=<?= $a['activityID'] ?>" class="btn btn-primary">Take Activity</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($activities)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No activities available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>