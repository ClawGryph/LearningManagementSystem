<?php
include '../db.php';
session_start();

$studentID = $_SESSION['user_id'] ?? null;
$courses = [];

if($studentID) {
    $stmt = $conn->prepare("
        SELECT 
            c.courseCode, 
            c.courseName, 
            CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name,
            ic.instructor_courseID
        FROM instructor_courses ic 
        JOIN courses c ON ic.courseID = c.courseID 
        JOIN users i ON ic.instructorID = i.userID 
        JOIN instructor_student_load isl ON ic.instructor_courseID = isl.instructor_courseID 
        WHERE isl.studentID = ?;
    ");
    $stmt->bind_param("i", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    $stmt->close();
}
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <h2>Subjects</h2>
            <div>
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="codeInput" placeholder="Enter class code" autocomplete="off">
                        <button id="joinButton"><i class="fa-solid fa-plus"></i> Join class</button>
                    </div>
                    <div id="suggestions" class="suggestions-list"></div>
                    <div id="searchMessage" class="search-message"></div>
                </div>
            </div>
            <div class="course-card-container">
                <?php foreach ($courses as $course): ?>
                        <form action="" method="POST" class="course-card-form">
                            <input type="hidden" name="instructor_courseID" value="<?= $course['instructor_courseID'] ?>">
                            <button type="submit" class="course-card">
                                <div class="course-card-header">
                                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                                </div>
                                <p><span>Professor:</span> <?= htmlspecialchars($course['Instructor_Name']) ?></p>
                            </button>
                        </form>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                    <p>You are not enrolled in any class yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>