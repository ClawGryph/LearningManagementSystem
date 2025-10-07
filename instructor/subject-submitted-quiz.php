<?php
include '../action/get-course-title.php';
include '../db.php';

// Fetch submitted quizzes
$submittedQuizzes = [];
$sql = "SELECT CONCAT(s.firstName, ' ', s.lastName) AS Student_Name, s.profileImage, sq.title, sq.max_score, sa.submission_date, sa.score, sa.tabs_open, sa.student_id, sa.assessment_authorID
        FROM student_assessments sa 
        JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID
        JOIN users s ON sa.student_id = s.userID
        JOIN quizzes sq ON aa.assessment_refID = sq.quizID
        WHERE aa.assessment_type = 'quiz' AND sa.status IN ('submitted', 'graded', 'late');";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $submittedQuizzes[] = $row;
    }
    $result->free();
}
$conn->close();
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <div class="page-header">
                <h2><?php echo htmlspecialchars($courseName ?? 'Unknown Course'); ?></h2>
                <h3>Submitted Quizzes</h3>
            </div>
                <form class="table-container" action="">
                    <table class="table-content">
                        <thead>
                            <th></th>
                            <th>Title</th>
                            <th>Submitted by</th>
                            <th>Date Submitted</th>
                            <th>View</th>
                            <th>Tabs open</th>
                            <th>Score</th>
                            <th>Action</th>
                        </thead>
                        <tbody class="table-body">
                            <?php if (!empty($submittedQuizzes)): ?>
                                <?php foreach ($submittedQuizzes as $submission): ?>
                                    <tr>
                                        <!-- Profile Image -->
                                        <td>
                                            <?php if (!empty($submission['profileImage'])): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($submission['profileImage']); ?>" 
                                                    alt="Profile" 
                                                    class="profile-img">
                                            <?php else: ?>
                                                <img src="../assets/default-profile.png" 
                                                    alt="Default Profile" 
                                                    class="profile-img">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['Student_Name']); ?></td>
                                        <td><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($submission['submission_date']))); ?></td>
                                        <td>
                                            <button type="button" class="home-contentBtn view-btn btn-accent2-bg"
                                                data-student-id="<?php echo htmlspecialchars($submission['student_id']); ?>"
                                                data-assessment-id="<?php echo htmlspecialchars($submission['assessment_authorID']); ?>">
                                                View
                                            </button>
                                        </td>
                                        <td><?php echo htmlspecialchars($submission['tabs_open']); ?></td>
                                        <td>
                                            <?php echo isset($submission['score']) 
                                                ? (int)htmlspecialchars($submission['score']) . ' / ' . htmlspecialchars($submission['max_score']) 
                                                : 'Not graded'; ?>
                                        </td>
                                        <td>
                                            <button type='button' class='home-contentBtn editBtn btn-accent-bg'>
                                                <i class='fa-solid fa-pen-to-square'></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No submitted quizzes found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>

        </div>

        <!-- Modal for viewing answers -->
        <div id="answersModal" style="display:none;">
            <div class="modal-content">
                <span id="closeModal">&times;</span>
                <h3>Student Answers</h3>
                <div id="answersContainer"></div>
            </div>
        </div>
    </div>
</div>