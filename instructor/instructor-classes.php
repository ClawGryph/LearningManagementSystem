<?php
session_start();
include '../db.php';

$instructorID = $_SESSION['user_id'] ?? null;

$courses = [];

if ($instructorID) {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(CASE WHEN isl.status = 'approved' THEN isl.studentID END) AS Number_of_students_enrolled, 
            c.courseCode, 
            c.courseName, 
            ic.courseID,
            ic.code,
            cl.classID,
            cl.section
            FROM instructor_courses ic 
            LEFT JOIN instructor_student_load isl 
            ON ic.instructor_courseID = isl.instructor_courseID 
            JOIN courses c 
            ON ic.courseID = c.courseID 
            JOIN class cl 
            ON ic.classID = cl.classID
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
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <div class="page-header">
                <h2>Classes</h2>
                <div class="clock">
                    <span id="hr">00</span>
                    <span>:</span>
                    <span id="min">00</span>
                    <span>:</span>
                    <span id="sec">00</span>
                </div>
            </div>
            <div class="course-card-container">
                <?php foreach ($courses as $course): ?>
                        <form action="subject-landingpage.php" method="POST" class="course-card-form">
                            <input type="hidden" name="courseID" value="<?= $course['courseID'] ?>">
                            <input type="hidden" name="classID" value="<?= $course['classID'] ?>">
                            <button type="submit" class="course-card">
                                <div class="course-card-header">
                                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                                    <p class="card-tag"><?= htmlspecialchars($course['section']) ?></p>
                                </div>
                                <div class="card-details">
                                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                                    <p>Number of Students Enrolled: <span><?= htmlspecialchars($course['Number_of_students_enrolled']) ?></span></p>

                                    <div class="card-footer">
                                        <p>Class Code: <span><?= htmlspecialchars($course['code']) ?></span></p>
                                    </div>
                                </div>
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