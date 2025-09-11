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

$courseID = $_POST['courseID'] ?? $_SESSION['courseID'] ?? null;

if (!$courseID) {
    die("No course selected.");
}

$_SESSION['courseID'] = $courseID;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=left_panel_close" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
                <a href="instructor-landingpage.php">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="link_name">Back</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="instructor-landingpage.php" class="link_name">Back</a></li>
                </ul>
            </li>

            <!-- 2 -->
            <li>
                <a href="#" data-content="subject-approval.php">
                    <i class="fa-solid fa-book-open"></i>
                    <span class="link_name">Approval Queue</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="subject-approval.php" class="link_name">Approval Queue</a></li>
                </ul>
            </li>

            <!-- 3 -->
            <li>
                <a href="#" data-content="subject-task-progress.php">
                    <i class="fa-solid fa-bars-progress"></i>
                    <span class="link_name">Task Progress</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="subject-task-progress.php?courseID=<?= $_GET['courseID'] ?? '' ?>" class="link_name">Task Progress</a></li>
                </ul>
            </li>

            <!-- 4 -->
            <li>
                <a href="#" data-content="subject-submitted-assignment.php">
                    <i class="fa-solid fa-file"></i>
                    <span class="link_name">Assignment Score</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="subject-submitted-assignment.php" class="link_name">Assignment Score</a></li>
                </ul>
            </li>

            <!-- 5 -->
            <li>
                <a href="#" data-content="subject-submitted-activity.php">
                    <i class="fa-solid fa-laptop-code"></i>
                    <span class="link_name">Activity Score</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="subject-submitted-activity.php" class="link_name">Activity Score</a></li>
                </ul>
            </li>

            <!-- 6 -->
            <li>
                <a href="#" data-content="subject-submitted-quiz.php">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span class="link_name">Quiz Score</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="subject-submitted-quiz.php" class="link_name">Quiz Score</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/loadContents.js"></script>
    <script src="../js/imageUpload.js"></script>
    <script src="../js/linkView.js"></script>
    <script src="../js/hideSidebar.js"></script>
    <script src="../js/studentCourseBar.js"></script>
    <script src="../js/enroleesQueue.js"></script>
    <script src="../js/openModal.js"></script>
</body>
</html>