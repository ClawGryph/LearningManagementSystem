<?php
session_start();
include '../db.php';

$user_id = $_SESSION['user_id'];

// Fetch all pending learning materials
$notifQuery = $conn->prepare("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, 
                            aa.assessment_type, 
                            aa.upload_date,
                            CONCAT(c.courseCode, ' - ', c.courseName) AS Class_Name,
                            sa.record_id 
                            FROM student_assessments sa 
                            JOIN assessment_author aa ON sa.assessment_authorID = aa.assessment_authorID 
                            JOIN instructor_courses ic ON aa.instructor_courseID = ic.instructor_courseID 
                            JOIN users i ON ic.instructorID = i.userID JOIN courses c ON ic.courseID = c.courseID 
                            WHERE sa.student_id = ? AND sa.status = 'assigned' AND sa.is_read = 0;
");
$notifQuery->bind_param("i", $user_id);
$notifQuery->execute();
$result = $notifQuery->get_result();
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Notification</h2>
                <form action="../action/markStudentNotificationsRead.php" method="POST">
                    <div class="notification-controls">
                        <button type="submit" class="home-contentBtn btn-drk-bg"><i class="fa-regular fa-circle-check"></i>Read</button>
                        <div>
                            <input type="checkbox" id="checkAll" name="choices" value="checkAll">
                            <label for="checkAll">Check All</label>
                        </div>
                    </div>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($notif = $result->fetch_assoc()): ?>
                        <a href="#"  class="notification-link">
                            <div class="notification-item">
                                <label class="notification-label">
                                    <input type="checkbox" name="notifications[]" value="<?= htmlspecialchars($notif['record_id']) ?>" class="notification-checkbox">
                                    <div class="notification-content">
                                        <p class="page-header">
                                            <span><strong><?= htmlspecialchars($notif['Instructor_Name']) ?></strong>&nbsp;</span>
                                            added <?= htmlspecialchars($notif['assessment_type']) ?> in &nbsp;
                                            <span><strong><?= htmlspecialchars($notif['Class_Name']) ?></strong></span>
                                        </p>
                                        <p class="notif-date">
                                            <span><i class="fa-solid fa-calendar-days"></i>
                                                <?= date("F j, Y g:i A", strtotime($notif['upload_date'])) ?>
                                            </span>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </a>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <p>No new notifications.</p>
                    <?php endif; ?>
                </form>
        </div>
    </div>
</div>