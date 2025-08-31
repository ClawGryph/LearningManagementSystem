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

$instructorCourseID = $_POST['instructor_courseID'] ?? $_SESSION['instructor_courseID'] ?? null;
if (!$instructorCourseID) {
    die("No course selected.");
}

$_SESSION['instructor_courseID'] = $instructorCourseID;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="sidebar">
        <div class="logo-details">
            <i class="fa-solid fa-school-flag"></i>
            <span class="logo_name">CTU - LMS</span>
        </div>
        <ul class="nav-links">
            <!-- 1 -->
            <li>
                <a href="student-landingpage.php">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="link_name">Back</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-landingpage.php" class="link_name">Back</a></li>
                </ul>
            </li>

            <!-- 2 -->
            <li>
                <a href="#" data-content="student-subject-myProgess.php">
                    <i class="fa-solid fa-bars-progress"></i>
                    <span class="link_name">My Progress</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-subject-myProgress.php" class="link_name">My Progress</a></li>
                </ul>
            </li>

            <!-- 3 -->
            <li>
                <a href="#" data-content="student-subject-assignment.php">
                    <i class="fa-solid fa-file"></i>
                    <span class="link_name">Assignment</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-subject-assignment.php" class="link_name">Assignment</a></li>
                </ul>
            </li>

            <!-- 4 -->
            <li>
                <a href="#" data-content="student-subject-activity.php">
                    <i class="fa-solid fa-laptop-code"></i>
                    <span class="link_name">Activity</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-subject-activity.php" class="link_name">Activity</a></li>
                </ul>
            </li>

            <!-- 5 -->
            <li>
                <a href="#" data-content="student-subject-quiz.php">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span class="link_name">Quiz</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-subject-quiz.php" class="link_name">Quiz</a></li>
                </ul>
            </li>

            <!-- 6 -->
            <li>
                <a href="#" data-content="student-subject-materials.php">
                    <i class="fa-solid fa-book"></i>
                    <span class="link_name">Learning Materials</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="student-subject-materials.php" class="link_name">Learning Materials</a></li>
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
                    <a href="../index.php"><i class="fa-solid fa-right-from-bracket"></i></a>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../js/progressBars.js"></script>
    <script src="../js/showAssignmentFile.js"></script>
</body>
</html>