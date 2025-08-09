<?php
session_start();
include '../db.php';

$instructorID = $_SESSION['user_id'] ?? null;

$courses = [];

if ($instructorID) {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(isl.studentID) AS Number_of_students_enrolled, 
            c.courseCode, 
            c.courseName, 
            ic.courseID,
            ic.code 
            FROM instructor_courses ic 
            LEFT JOIN instructor_student_load isl 
            ON ic.instructor_courseID = isl.instructor_courseID 
            JOIN courses c 
            ON ic.courseID = c.courseID 
            WHERE ic.instructorID = ?
            GROUP BY c.courseCode, c.courseName, ic.code;
    ");
    $stmt->bind_param("i", $instructorID);
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
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Classes</h2>
            <div class="course-card-container">
                <?php foreach ($courses as $course): ?>
                        <form action="subject-landingpage.php" method="POST" class="course-card-form">
                            <input type="hidden" name="courseID" value="<?= $course['courseID'] ?>">
                            <button type="submit" class="course-card">
                                <div class="course-card-header">
                                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                                </div>
                                <p><span>Number of Students Enrolled:</span> <?= htmlspecialchars($course['Number_of_students_enrolled']) ?></p>
                                <p><span>Class Code:</span> <?= htmlspecialchars($course['code']) ?></p>
                            </button>
                        </form>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                    <p>No courses available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>