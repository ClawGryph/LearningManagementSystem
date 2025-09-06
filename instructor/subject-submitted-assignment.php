<?php
include '../action/get-course-title.php';
include '../db.php';

// Fetch submitted assignments
$submittedAssignments = [];
$stmt = $conn->prepare("SELECT CONCAT(s.firstName, ' ', s.lastName) AS Student_Name, s.profileImage, a.max_score, ss.submitted_at, ss.file_path 
                        FROM student_submissions ss 
                        JOIN assessment_author aa ON ss.assessment_authorID = aa.assessment_authorID 
                        JOIN users s ON ss.student_id = s.userID 
                        JOIN assignment a ON aa.assessment_refID = a.assignmentID
                        WHERE aa.assessment_type = 'assignment';");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $submittedAssignments[] = $row;
}
$stmt->close();

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
                <h3>Submitted Assignment</h3>
            </div>
                <form class="table-container" action="">
                    <table class="table-content">
                        <thead>
                            <th></th>
                            <th>Submitted by</th>
                            <th>Date Submitted</th>
                            <th>Download</th>
                            <th>Score</th>
                            <th>Action</th>
                        </thead>
                        <tbody class="table-body">
                            <?php if (!empty($submittedAssignments)): ?>
                                <?php foreach ($submittedAssignments as $submission): ?>
                                    <tr>
                                        <!-- Profile Image -->
                                        <td>
                                            <?php if (!empty($submission['profileImage'])): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($submission['profileImage']); ?>" 
                                                    alt="Profile" 
                                                    style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                                            <?php else: ?>
                                                <img src="../assets/default-profile.png" 
                                                    alt="Default Profile" 
                                                    style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                                            <?php endif; ?>
                                        </td>

                                        <!-- Student Name -->
                                        <td><?php echo htmlspecialchars($submission['Student_Name']); ?></td>

                                        <!-- Date Submitted -->
                                        <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>

                                        <!-- Download -->
                                        <td>
                                            <?php if (!empty($submission['file_path'])): ?>
                                                <a href="<?php echo htmlspecialchars($submission['file_path']); ?>" 
                                                download 
                                                class="btn btn-download">
                                                    Download
                                                </a>
                                            <?php else: ?>
                                                No file
                                            <?php endif; ?>
                                        </td>

                                        <!-- Score Input -->
                                        <td>
                                            <input type="number" 
                                                name="score[<?php echo $submission['Student_Name']; ?>]" 
                                                min="0" 
                                                max="<?php echo htmlspecialchars($submission['max_score']); ?>" 
                                                placeholder="0" 
                                                style="width:60px;">
                                            / <?php echo htmlspecialchars($submission['max_score']); ?>
                                        </td>

                                        <!-- Action -->
                                        <td>
                                            <button type="submit" 
                                                    name="saveScore" 
                                                    value="<?php echo htmlspecialchars($submission['Student_Name']); ?>" 
                                                    class="btn btn-save">
                                                Save
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No submissions yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
        </div>
    </div>
</div>