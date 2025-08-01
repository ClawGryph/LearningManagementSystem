<?php
include '../db.php';

// Fetch all pending learning materials
$notifQuery = $conn->query("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, lma.lmID, lma.request_date 
FROM learningmaterials_author lma 
JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
JOIN users i ON ic.instructorID = i.userID 
JOIN courses c ON ic.courseID = c.courseID 
JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID 
WHERE lma.is_read = 0 AND clm.status = 'pending';
");
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Welcome, Admin</h2>
                <form action="../action/markNotificationsRead.php" method="POST">
                    <div class="notification-controls">
                        <button type="submit" class="home-contentBtn btn-drk-bg"><i class="fa-regular fa-circle-check"></i>Read</button>
                        <div>
                            <input type="checkbox" id="checkAll" name="choices" value="checkAll">
                            <label for="checkAll">Check All</label>
                        </div>
                    </div>
                    <?php if ($notifQuery->num_rows > 0): ?>
                    <?php while ($notif = $notifQuery->fetch_assoc()): ?>
                        <a href="#" data-content="admin-lm-lists.php" class="notification-link">
                            <div class="notification-item">
                                <label class="notification-label">
                                    <input type="checkbox" name="notifications[]" value="<?= htmlspecialchars($notif['lmID']) ?>" class="notification-checkbox">
                                    <div class="notification-content">
                                        <p class="page-header">
                                            <span><strong><?= htmlspecialchars($notif['Instructor_Name']) ?></strong>&nbsp;</span>
                                            added a course material to the course&nbsp;
                                            <span><strong><?= htmlspecialchars($notif['Course_Name']) ?></strong></span>
                                        </p>
                                        <p class="notif-date">
                                            <span><i class="fa-solid fa-calendar-days"></i>
                                                <?= date("F j, Y g:i A", strtotime($notif['request_date'])) ?>
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