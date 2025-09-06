<?php
include '../action/get-course-title.php';
include '../db.php';

// Fetch submitted quizzes
$submittedQuizzes = [];
$sql = "SELECT CONCAT(s.firstName, ' ', s.lastName) AS Student_Name, s.profileImage, sq.title, sq.max_score, sa.submission_date, sa.score, sa.student_id, sa.assessment_authorID
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
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <div class="page-header">
                <h2><?php echo htmlspecialchars($courseName ?? 'Unknown Course'); ?></h2>
            </div>
                <form class="table-container" action="">
                    <table class="table-content">
                        <thead>
                            <th></th>
                            <th>Title</th>
                            <th>Submitted by</th>
                            <th>Date Submitted</th>
                            <th>View</th>
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
                                                    style="width:40px; height:40px; border:1px solid black; border-radius:50%; object-fit:cover;">
                                            <?php else: ?>
                                                <img src="../assets/default-profile.png" 
                                                    alt="Default Profile" 
                                                    style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['Student_Name']); ?></td>
                                        <td><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($submission['submission_date']))); ?></td>
                                        <td>
                                            <button type="button" class="view-btn"
                                                data-student-id="<?php echo htmlspecialchars($submission['student_id']); ?>"
                                                data-assessment-id="<?php echo htmlspecialchars($submission['assessment_authorID']); ?>">
                                                View
                                            </button>
                                        </td>
                                        <td>
                                            <?php echo isset($submission['score']) 
                                                ? htmlspecialchars($submission['score']) . ' / ' . htmlspecialchars($submission['max_score']) 
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