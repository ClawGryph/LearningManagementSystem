<?php
session_start();
include '../db.php';

$instructorID = $_SESSION['user_id'] ?? null;

$courses = [];

if ($instructorID) {
    $stmt = $conn->prepare("
        SELECT c.courseCode, c.courseName, ic.code
        FROM instructor_courses ic
        JOIN courses c ON ic.courseID = c.courseID
        WHERE ic.instructorID = ?
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
            <?php foreach ($courses as $course): ?>
                <a href="#" class="course-card">
                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                    <p><span>Class Code:</span> <?= htmlspecialchars($course['code']) ?></p>
                </a>
            <?php endforeach; ?>
            <?php if (empty($courses)): ?>
                <p>No courses available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>