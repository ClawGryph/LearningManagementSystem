<?php
include '../action/get-course-title.php';
include '../db.php';

// Fetch submitted activities
$submittedActivities = [];
$sql = "SELECT CONCAT(s.firstName, ' ', s.lastName) AS Student_Name, s.profileImage,pa.title, pa.max_score, ss.submitted_at, ss.code_submission, sa.tabs_open, sa.score
        FROM student_submissions ss 
        JOIN assessment_author aa ON ss.assessment_authorID = aa.assessment_authorID 
        JOIN users s ON ss.student_id = s.userID 
        JOIN programming_activity pa ON aa.assessment_refID = pa.activityID
        LEFT JOIN student_assessments sa ON s.userID = sa.student_id AND aa.assessment_authorID = sa.assessment_authorID
        WHERE aa.assessment_type = 'activity' AND sa.status IN ('submitted', 'graded', 'late');";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $submittedActivities[] = $row;
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
            <div>
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
                            <?php if (!empty($submittedActivities)): ?>
                                <?php foreach ($submittedActivities as $submission): ?>
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

                                        <!-- TITLE -->
                                        <td><?php echo htmlspecialchars($submission['title']); ?></td>

                                        <!-- Student Name -->
                                        <td><?php echo htmlspecialchars($submission['Student_Name']); ?></td>

                                        <!-- Date Submitted -->
                                        <td><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($submission['submitted_at']))); ?></td>

                                        <!-- View Code Submission -->
                                        <td>
                                            <?php if (!empty($submission['code_submission'])): ?>
                                                <a href="#" class="home-contentBtn view-code-btn btn-accent2-bg" data-code="<?php echo htmlspecialchars($submission['code_submission']); ?>">View</a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tabs Open -->
                                        <td><?php echo htmlspecialchars($submission['tabs_open'] ?? 'N/A'); ?></td>

                                        <!-- Score -->
                                        <td><?php echo isset($submission['score']) ? htmlspecialchars($submission['score'] . ' / ' . $submission['max_score']) : 'Not graded'; ?></td>

                                        <!-- Action -->
                                        <td>
                                            <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No submissions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>