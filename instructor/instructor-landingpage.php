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

$notif = $conn->query("SELECT COUNT(*) AS notif_count FROM learningmaterials_author lma
                        JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID
                        WHERE (clm.status = 'rejected' OR clm.status = 'approved') AND lma.instructor_read = 0");
$row = $notif->fetch_assoc();
$notifCount = $row['notif_count'];

$join = $conn->query("SELECT COUNT(*) AS join_count
    FROM instructor_student_load isl
    WHERE isl.status = 'pending' AND isl.is_read = 0");
$joinRow = $join->fetch_assoc();
$joinCount = $joinRow['join_count'];

// Fetch all pending learning materials
$notifQuery = $conn->query("SELECT CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, cl.section, clm.status, lma.lmID, lma.decision_date 
FROM learningmaterials_author lma 
JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
JOIN courses c ON ic.courseID = c.courseID 
JOIN class cl ON ic.classID = cl.classID
JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID 
WHERE (clm.status = 'rejected' OR clm.status = 'approved') AND lma.instructor_read = 0;
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
    WHERE isl.status = 'pending' AND isl.is_read = 0
    ORDER BY isl.request_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor | Dashboard</title>
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
                <a href="#" data-content="instructor-classes.php">
                    <i class="fa-solid fa-person-chalkboard"></i>
                    <span class="link_name">My class</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-classes.php" class="link_name">My class</a></li>
                </ul>
            </li>

            <!-- 2 -->
            <li>
                <a href="#" data-content="instructor-create-quiz.php">
                    <i class="fa-solid fa-clipboard"></i>
                    <span class="link_name">Manage Quiz</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-create-quiz.php" class="link_name">Manage Quiz</a></li>
                </ul>
            </li>

            <!-- 3 -->
            <li>
                <a href="#" data-content="instructor-create-assignment.php">
                    <i class="fa-solid fa-file"></i>
                    <span class="link_name">Manage Assignment</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-create-assignment.php" class="link_name">Manage Assignment</a></li>
                </ul>
            </li>

            <!-- 4 -->
            <li>
                <a href="#" data-content="instructor-create-activity.php">
                    <i class="fa-solid fa-laptop-code"></i>
                    <span class="link_name">Manage Activity</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-create-activity.php" class="link_name">Manage Activity</a></li>
                </ul>
            </li>

            <!-- 5 -->
            <li>
                <a href="#" data-content="instructor-upload-lm.php">
                    <i class="fa-solid fa-upload"></i>
                    <span class="link_name">Add Resources</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-upload-lm.php" class="link_name">Add Resources</a></li>
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
                    <?php if ($notifCount > 0): ?>
                        <span class="notif-badge"><?= $notifCount ?></span>
                    <?php else: ?>
                        <span class="notif-badge">0</span>
                    <?php endif; ?>
                </a>

                <!-- NOTIFICATION DETAILS DROPDOWN -->
                <div class="notif_details hidden">
                    <div class="notif-header">
                        <h4>Notifications</h4>
                        <form action="../action/markRead.php" method="POST" id="markReadForm">
                            <button type="submit" class="mark-read-btn btn-drk-bg">
                                <i class="fa-regular fa-circle-check"></i> Read
                            </button>
                        </form>
                    </div>

                    <div class="notif-list">
                        <?php if ($notifQuery->num_rows > 0): ?>
                            <?php while ($notif = $notifQuery->fetch_assoc()): ?>
                                <div class="notif-item" data-notif-id="lm_<?= $notif['lmID'] ?>">
                                    <div class="notif-icon"><i class="fa-solid fa-book"></i></div>
                                    <div class="notif-content">
                                        <p>
                                            Admin <strong><?= htmlspecialchars($notif['status']) ?></strong>
                                            the learning materials in
                                            <strong><?= htmlspecialchars($notif['Course_Name']) ?></strong>
                                            (Section <?= htmlspecialchars($notif['section']) ?>)
                                        </p>
                                        <span class="notif-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date("F j, Y g:i A", strtotime($notif['decision_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if ($joinQuery->num_rows > 0): ?>
                            <?php while ($join = $joinQuery->fetch_assoc()): ?>
                                <div class="notif-item" data-notif-id="join_<?= $join['instructor_student_loadID'] ?>">
                                    <div class="notif-icon"><i class="fa-solid fa-user-plus"></i></div>
                                    <div class="notif-content">
                                        <p>
                                            Student <strong><?= htmlspecialchars($join['firstName'] . ' ' . $join['lastName']) ?></strong>
                                            requested to join
                                            <strong><?= htmlspecialchars($join['courseCode'] . ' - ' . $join['courseName']) ?></strong>
                                            (Section <?= htmlspecialchars($join['section']) ?>)
                                        </p>
                                        <span class="notif-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date("F j, Y g:i A", strtotime($join['request_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if ($notifQuery->num_rows === 0 && $joinQuery->num_rows === 0): ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/clock.js"></script>
    <script src="../js/imageUpload.js"></script>
    <script src="../js/linkView.js"></script>
    <script src="../js/notifToggle.js"></script>
    <script src="../js/quiz.js"></script>
    <script src="../js/questionToggle.js"></script>
    <script src="../js/assignment.js"></script>
    <script src="../js/showAssignmentFile.js"></script>
    <script src="../js/activity.js"></script>
    <script src="../js/materials.js"></script>
    <script src="../js/instructorNotif.js"></script>
</body>
</html>