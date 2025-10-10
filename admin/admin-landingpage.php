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
                        WHERE lma.is_read = 0 AND clm.status = 'pending'");
$row = $notif->fetch_assoc();
$notifCount = $row['notif_count'];

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
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
            <li>
                <a href="#" data-content="admin-dashboard.php">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span class="link_name">Dashboard</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="admin-dashboard.php" class="link_name">Dashboard</a></li>
                </ul>
            </li>
            <li>
                <a href="#" data-content="admin-create-class.php">
                    <i class="fa-solid fa-book-open"></i>
                    <span class="link_name">Create Class</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="admin-create-class.php" class="link_name">Create Class</a></li>
                </ul>
            </li>
            <li>
                <div class="icon-link" >
                    <a href="">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span class="link_name">Courses</span>
                    </a>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <span class="sub-menu-header">Courses</span>
                    <li><a href="#" data-content="admin-create-courses.php" class="link_name">Create Courses</a></li>
                    <li><a href="#" data-content="admin-instructor-courses.php" class="link_name">Add Instructor to Courses</a></li>
                    <li><a href="#" data-content="admin-instructor-load.php" class="link_name">Instructors Load</a></li>
                </ul>
            </li>
            <li>
                <a href="#" data-content="admin-lm-lists.php">
                    <i class="fa-solid fa-book-open"></i>
                    <span class="link_name">Approval Queue</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="admin-lm-lists.php" class="link_name">Approval Queue</a></li>
                </ul>
            </li>
            <li>
                <div class="profile-details">
                    <div class="profile-content">

                        <!-- Image preview area -->
                        <img src="../uploads/<?= $profileImage ?>" id="profileImagePreview" alt="Profile Image">
                        <div class="overlay-text"><i class="fa-solid fa-camera"></i></div>

                        <!-- Trigger file input on click -->
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


                <!-- NOTIFICATION DETAILS -->
                <div class="notif_details hidden">
                    <div class="notif-header">
                        <h4>Notifications</h4>
                        <form action="../action/markNotificationsRead.php" method="POST" id="markReadForm">
                            <button type="submit" class="mark-read-btn btn-drk-bg">
                            <i class="fa-regular fa-circle-check"></i> Read
                            </button>
                        </form>
                    </div>

                    <div class="notif-list">
                        <?php if ($notifQuery->num_rows > 0): ?>
                            <?php while ($notif = $notifQuery->fetch_assoc()): ?>
                            <div class="notif-item">
                                <div class="notif-icon"><i class="fa-solid fa-book"></i></div>
                                <div class="notif-content">
                                    <p>
                                        <strong><?= htmlspecialchars($notif['Instructor_Name']) ?></strong>
                                        added a course material to
                                        <strong><?= htmlspecialchars($notif['Course_Name']) ?></strong>
                                    </p>
                                    <span class="notif-date">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <?= date("F j, Y g:i A", strtotime($notif['request_date'])) ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/enrolledStudents.js"></script>
    <script src="../js/class.js"></script>
    <script src="../js/course.js"></script>
    <script src="../js/instructorLoad.js"></script>
    <script src="../js/lm-lists.js"></script>
</body>
</html>