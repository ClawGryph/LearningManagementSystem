<?php
include '../db.php';
session_start();

$studentID = $_SESSION['user_id']; 
$instructor_courseID = $_SESSION['instructor_courseID']; 

$sql = "
    SELECT q.quizID, q.title, q.description, aa.assessment_time, q.deadline, sa.status
    FROM student_assessments sa
    JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
    JOIN quizzes q ON aa.assessment_refID = q.quizID
    WHERE sa.student_id = ?
      AND aa.instructor_courseID = ?
      AND aa.assessment_type = 'quiz'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentID, $instructor_courseID);
$stmt->execute();
$result = $stmt->get_result();

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <h2>Quizzes</h2>
            
            <div class="table-container">
                <table class="table-content">
                    <thead>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Time Duration</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </thead>
                    <tbody class="table-body">
                        <?php foreach ($quizzes as $q): ?>
                            <tr>
                                <td><?= htmlspecialchars($q['title']) ?></td>
                                <td><?= htmlspecialchars($q['description']) ?></td>
                                <td><?= htmlspecialchars($q['assessment_time']) ?> mins</td>
                                <td><?= htmlspecialchars($q['deadline']) ?></td>
                                <td>
                                    <?php if ($q['status'] === 'submitted' || $q['status'] === 'graded'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php elseif ($q['status'] === 'late'): ?>
                                        <span class="badge bg-danger">Late</span>
                                    <?php else: ?>
                                        <a href="student-subject-takeQuiz.php?quizID=<?= $q['quizID'] ?>" class="btn btn-primary">Take Quiz</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($quizzes)): ?>
                            <tr><td colspan="5">No quizzes assigned.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>