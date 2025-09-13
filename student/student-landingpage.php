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
    <nav class="sidebar">
        <div class="logo-details">
            <img src="../images/logo.png" alt="Open book logo" class="logo_img">
            <span class="logo_name">CogniCore</span>
            <span class="fa-bars material-symbols-outlined">
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

            <!-- 2 -->
            <li>
                <a href="#" data-content="student-notification.php" class="notif">
                    <i class="fa-solid fa-bell"></i>

                    <!-- Notification Badge -->
                    <?php $totalCount = $notifCount + $joinCount + $materialsCount; ?>
                    <span class="notif-badge"><?= $totalCount ?></span>

                    <span class="link_name">Notification</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-notification.php" class="link_name">Notification</a></li> 
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
    <main class="home-section" id="main-content">
        
    </main>
    <script>
        const currentUserRole = "<?= $_SESSION['role'] ?? '' ?>";
    </script>
    <script src="../js/loadContents.js"></script>
    <script src="../js/imageUpload.js"></script>
    <script src="../js/linkView.js"></script>
    <script src="../js/hideSidebar.js"></script>
    <script src="../js/checkAll.js"></script>
    <script src="../js/studentCourses.js"></script>
</body>
</html>