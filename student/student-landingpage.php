<?php
session_start();
include '../db.php';

$userId = $_SESSION['user_id'] ?? null;
$userFullName = '';
$userRole = '';

if ($userId) {
    $stmt = $conn->prepare("SELECT firstName, lastName, role, profileImage FROM users WHERE userID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $role, $profileImage);
    if ($stmt->fetch()) {
        $userFullName = htmlspecialchars($firstName . ' ' . $lastName);
        $userRole = htmlspecialchars(ucfirst($role));
        $profileImage = $profileImage ?: 'default.png';
    }
    $stmt->close();
}

$notif = $conn->prepare("SELECT COUNT(*) AS notif_count FROM student_assessments 
                         WHERE status = 'assigned' AND student_id = ? AND is_read = 0");
$notif->bind_param('i', $userId);
$notif->execute();
$result = $notif->get_result();
$row = $result->fetch_assoc();
$notifCount = $row['notif_count'];

$joinNotif = $conn->prepare("SELECT COUNT(*) AS join_count
    FROM instructor_student_load isl
    WHERE (isl.status = 'approved' OR isl.status = 'rejected') AND isl.student_read = 0 AND isl.studentID = ?");
$joinNotif->bind_param('i', $userId);
$joinNotif->execute();
$joinResult = $joinNotif->get_result();
$joinRow = $joinResult->fetch_assoc();
$joinCount = $joinRow['join_count'];

$materialsNotif = $conn->prepare("
    SELECT COUNT(*) AS materials_count
    FROM learningmaterials_author lma
    INNER JOIN course_learningmaterials cla ON lma.course_lmID = cla.course_lmID
    INNER JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID
    INNER JOIN instructor_student_load isl ON ic.instructor_courseID = isl.instructor_courseID
    WHERE cla.status = 'approved'
      AND isl.studentID = ?
      AND lma.student_read = 0
");
$materialsNotif->bind_param('i', $userId);
$materialsNotif->execute();
$materialsResult = $materialsNotif->get_result();
$materialsRow = $materialsResult->fetch_assoc();
$materialsCount = $materialsRow['materials_count'];

$totalNotifCount = $notifCount + $joinCount + $materialsCount;

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
$notifQuery->bind_param("i", $userId);
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
$materialsQuery->bind_param("i", $userId);
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
$joinQuery->bind_param("i", $userId);
$joinQuery->execute();
$joinResult = $joinQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=left_panel_close" />
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="sidebar hidden">
        <div class="logo-details">
            <img src="../images/logo.png" alt="Open book logo" class="logo_img">
            <span class="logo_name">CogniCore</span>
            <span class="fa-bars material-symbols-outlined sidebar-panel">
                left_panel_close
            </span>
        </div>
        <ul class="nav-links">
            <!-- 1 -->
            <li>
                <a href="#" data-content="student-courses.php">
                    <i class="fa-solid fa-person-chalkboard"></i>
                    <span class="link_name">My courses</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-courses.php" class="link_name">My courses</a></li>
                </ul>
            </li>

            <li>
                <div class="profile-details">
                    <div class="profile-content">
                        <div class="overlay-text"><i class="fa-solid fa-camera"></i></div>

                        <!-- Image preview area -->
                        <img src="../uploads/<?= $profileImage ?>" id="profileImagePreview" alt="Profile Image">

                        <!-- Trigger file input on click (optional, for UX) -->
                        <input type="file" id="profileImageInput" style="display: none;" accept="image/*">

                        <!-- Actual form used for uploading -->
                        <form action="../action/uploadProfileImage.php" id="uploadProfileForm" enctype="multipart/form-data" style="display: none;" method="POST">
                            <input type="file" name="profileImage" id="profileImageRealInput" accept="image/*">
                        </form>
                    </div>

                    <div class="name-job">
                        <div class="profile_name"><?= $userFullName ?: 'User' ?></div>
                        <div class="job"><?= $userRole ?: 'Role' ?></div>
                    </div>
                    <a href="../index.php"><i class="fa-solid fa-right-from-bracket logout"></i></a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- NAVIGATION BAR MENU -->
     <nav class="navigation">
        <ul class="navigation__links">
            <li class="open_sidebar">
                <span class="fa-bars material-symbols-outlined navbar-panel">
                    left_panel_close
                </span>
            </li>
            <li class="clock">
                <span id="hr">00</span>
                <span>:</span>
                <span id="min">00</span>
                <span>:</span>
                <span id="sec">00</span>
            </li>
            <li class="navigation__notif hidden">
                <a href="#" class="notif">
                    <i class="fa-solid fa-bell notif-bell"></i>

                    <!-- Notification Badge -->
                    <span class="notif-badge">
                        <?= $totalNotifCount > 0 ? $totalNotifCount : 0 ?>
                    </span>
                </a>

                <!-- NOTIFICATION DETAILS DROPDOWN -->
                <div class="notif_details hidden">
                    <div class="notif-header">
                        <h4>Notifications</h4>
                        <form action="../action/markStudentNotificationsRead.php" method="POST" id="markReadForm">
                            <button type="submit" class="mark-read-btn btn-drk-bg">
                                <i class="fa-regular fa-circle-check"></i> Read
                            </button>
                        </form>
                    </div>

                    <div class="notif-list">
                        <!-- 🧩 Assessment Notifications -->
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($notif = $result->fetch_assoc()): ?>
                                <div class="notif-item">
                                    <div class="notif-icon"><i class="fa-solid fa-file-lines"></i></div>
                                    <div class="notif-content">
                                        <p>
                                            <strong><?= htmlspecialchars($notif['Instructor_Name']) ?></strong>
                                            added <?= htmlspecialchars($notif['assessment_type']) ?> in
                                            <strong><?= htmlspecialchars($notif['Class_Name']) ?></strong>
                                        </p>
                                        <span class="notif-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date("F j, Y g:i A", strtotime($notif['upload_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <!-- 📘 Learning Materials Notifications -->
                        <?php if ($materialsResult->num_rows > 0): ?>
                            <?php while ($materials = $materialsResult->fetch_assoc()): ?>
                                <div class="notif-item">
                                    <div class="notif-icon"><i class="fa-solid fa-book"></i></div>
                                    <div class="notif-content">
                                        <p>
                                            <strong><?= htmlspecialchars($materials['Instructor_Name']) ?></strong>
                                            added new learning materials in
                                            <strong><?= htmlspecialchars($materials['Class_Name']) ?></strong>
                                        </p>
                                        <span class="notif-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date("F j, Y g:i A", strtotime($materials['decision_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <!-- 👨‍🏫 Join Class Notifications -->
                        <?php if ($joinResult->num_rows > 0): ?>
                            <?php while ($join = $joinResult->fetch_assoc()): ?>
                                <div class="notif-item">
                                    <div class="notif-icon">
                                        <?php if ($join['status'] === 'approved'): ?>
                                            <i class="fa-solid fa-check-circle"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-times-circle"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="notif-content">
                                        <p>
                                            <strong><?= htmlspecialchars($join['Instructor_Name']) ?></strong>
                                            <strong><?= htmlspecialchars($join['status']) ?></strong>
                                            your request to join
                                            <strong><?= htmlspecialchars($join['Class_Name']) ?></strong>
                                        </p>
                                        <span class="notif-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date("F j, Y g:i A", strtotime($join['decision_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <!-- ❗ No Notifications -->
                        <?php if (
                            $result->num_rows === 0 &&
                            $materialsResult->num_rows === 0 &&
                            $joinResult->num_rows === 0
                        ): ?>
                            <p class="no-notif">No new notifications.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
    <main class="home-section" id="main-content">
        
    </main>
    <script>
        const currentUserRole = "<?= $_SESSION['role'] ?? '' ?>";
    </script>
    <script src="../js/loadContents.js"></script>
    <script src="../js/clock.js"></script>
    <script src="../js/imageUpload.js"></script>
    <script src="../js/linkView.js"></script>
    <script src="../js/notifToggle.js"></script>
    <script src="../js/checkAll.js"></script>
    <script src="../js/studentCourses.js"></script>
</body>
</html>