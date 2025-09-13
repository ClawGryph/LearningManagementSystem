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

//learning materials
$materialsQuery = $conn->prepare("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, 
                                CONCAT(c.courseCode, ' - ', c.courseName) AS Class_Name,
                                lma.decision_date,
                                lma.lmID
                                FROM learningmaterials_author lma
                                INNER JOIN course_learningmaterials cla ON lma.course_lmID = cla.course_lmID
                                INNER JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID
                                INNER JOIN instructor_student_load isl ON ic.instructor_courseID = isl.instructor_courseID
                                INNER JOIN users i ON ic.instructorID = i.userID
                                INNER JOIN courses c ON ic.courseID = c.courseID
                                WHERE cla.status = 'approved'
                                AND isl.studentID = ?
                                AND lma.student_read = 0");
$materialsQuery->bind_param("i", $user_id);
$materialsQuery->execute();
$materialsResult = $materialsQuery->get_result();

//Join Class
$joinQuery = $conn->prepare("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, 
                            CONCAT(c.courseCode, ' - ', c.courseName) AS Class_Name,
                            isl.status,
                            isl.decision_date,
                            isl.instructor_student_loadID
                            FROM instructor_student_load isl
                            JOIN instructor_courses ic ON isl.instructor_courseID = ic.instructor_courseID
                            JOIN users i ON ic.instructorID = i.userID
                            JOIN courses c ON ic.courseID = c.courseID
                            WHERE (isl.status = 'approved' OR isl.status = 'rejected')
                                AND isl.studentID = ?
                                AND isl.student_read = 0
                            ");
$joinQuery->bind_param("i", $user_id);
$joinQuery->execute();
$joinResult = $joinQuery->get_result();
?>

<div class="home-content">
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
                                    <input type="checkbox" name="notifications[]" value="assessment:<?= htmlspecialchars($notif['record_id']) ?>" class="notification-checkbox">
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
                    <?php endif; ?>

                    <!-- MATERIALS -->
                    <?php if($materialsResult->num_rows > 0): ?>
                    <?php while($materials = $materialsResult->fetch_assoc()): ?>
                        <a href="#"  class="notification-link">
                            <div class="notification-item">
                                <label class="notification-label">
                                    <input type="checkbox" name="notifications[]" value="material:<?= htmlspecialchars($materials['lmID']) ?>" class="notification-checkbox">
                                    <div class="notification-content">
                                        <p class="page-header">
                                            <span><strong><?= htmlspecialchars($materials['Instructor_Name']) ?></strong>&nbsp;</span>
                                            added new learning materials in &nbsp;
                                            <span><strong><?= htmlspecialchars($materials['Class_Name']) ?></strong></span>
                                        </p>
                                        <p class="notif-date">
                                            <span><i class="fa-solid fa-calendar-days"></i>
                                                <?= date("F j, Y g:i A", strtotime($materials['decision_date'])) ?>
                                            </span>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </a>
                    <?php endwhile; ?>
                    <?php endif; ?>

                    <!-- JOIN CLASS -->
                    <?php if($joinResult->num_rows > 0): ?>
                    <?php while($join = $joinResult->fetch_assoc()): ?>
                        <a href="#"  class="notification-link">
                            <div class="notification-item">
                                <label class="notification-label">
                                    <input type="checkbox" name="notifications[]" value="join:<?= htmlspecialchars($join['instructor_student_loadID']) ?>" class="notification-checkbox">
                                    <div class="notification-content">
                                        <p class="page-header">
                                            <span><strong><?= htmlspecialchars($join['Instructor_Name']) ?></strong>&nbsp;</span>
                                            <span><strong><?= htmlspecialchars($join['status']) ?></strong>&nbsp;</span>
                                            your request for joining the class &nbsp;
                                            <span><strong><?= htmlspecialchars($join['Class_Name']) ?></strong></span>
                                        </p>
                                        <p class="notif-date">
                                            <span><i class="fa-solid fa-calendar-days"></i>
                                                <?= date("F j, Y g:i A", strtotime($join['decision_date'])) ?>
                                            </span>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </a>
                    <?php endwhile; ?>
                    <?php endif; ?>

                    <?php if ($result->num_rows === 0 && $materialsResult->num_rows === 0 && $joinResult->num_rows === 0): ?>
                        <p>No new notifications.</p>
                    <?php endif; ?>
                </form>
        </div>
    </div>
</div>