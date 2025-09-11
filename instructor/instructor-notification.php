<?php
include '../db.php';

// Fetch all pending learning materials
$notifQuery = $conn->query("SELECT CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, cl.section, clm.status, lma.lmID, lma.decision_date 
FROM learningmaterials_author lma 
JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
JOIN courses c ON ic.courseID = c.courseID 
JOIN class cl ON ic.classID = cl.classID
JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID 
WHERE clm.status = 'rejected' OR clm.status = 'approved';
");

$joinQuery = $conn->query("
    SELECT isl.instructor_student_loadID AS request_id, isl.status, isl.request_date, isl.decision_date,
           s.firstName, s.lastName,
           c.courseCode, c.courseName, cl.section
    FROM instructor_student_load isl
    JOIN users s ON isl.studentID = s.userID
    JOIN instructor_courses ic ON isl.instructor_courseID = ic.instructor_courseID
    JOIN courses c ON ic.courseID = c.courseID
    JOIN class cl ON ic.classID = cl.classID
    WHERE isl.status = 'pending'
    ORDER BY isl.request_date DESC
");
?>

<div class="home-content">
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
                        <a href="#" class="notification-link">
                            <div class="notification-item">
                                <label class="notification-label">
                                    <input type="checkbox" name="notifications[]" value="<?= htmlspecialchars($notif['lmID']) ?>" class="notification-checkbox">
                                    <div class="notification-content">
                                        <p class="page-header">
                                            Admin&nbsp;
                                            <span><strong><?= htmlspecialchars($notif['status']) ?></strong>&nbsp;</span>
                                            the learning materials in&nbsp;
                                            <span><strong><?= htmlspecialchars($notif['Course_Name']) ?></strong></span>&nbsp;
                                            section&nbsp;
                                            <span><strong><?= htmlspecialchars($notif['section']) ?></strong></span>
                                        </p>
                                        <p class="notif-date">
                                            <span><i class="fa-solid fa-calendar-days"></i>
                                                <?= date("F j, Y g:i A", strtotime($notif['decision_date'])) ?>
                                            </span>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </a>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <!-- STUDENT JOIN NOTIF -->
                     <?php if ($joinQuery->num_rows > 0): ?>
                        <?php while ($join = $joinQuery->fetch_assoc()): ?>
                            <a href="#" class="notification-link">
                                <div class="notification-item">
                                    <label class="notification-label">
                                        <input type="checkbox" name="notifications[]" 
                                            value="<?= htmlspecialchars($join['request_id']) ?>" 
                                            class="notification-checkbox">
                                        <div class="notification-content">
                                            <p class="page-header">
                                                Student&nbsp; <strong><?= htmlspecialchars($join['firstName'] . " " . $join['lastName']) ?>&nbsp;</strong>
                                                request 
                                                to join&nbsp; <strong><?= htmlspecialchars($join['courseCode'] . " - " . $join['courseName']) ?>&nbsp;</strong>
                                                section&nbsp; <strong><?= htmlspecialchars($join['section']) ?></strong>
                                            </p>
                                            <p class="notif-date">
                                                <span><i class="fa-solid fa-calendar-days"></i>
                                                    <?= date("F j, Y g:i A", strtotime($join['status'] === 'pending' ? $join['request_date'] : $join['decision_date'])) ?>
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