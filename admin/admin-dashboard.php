<?php
include '../db.php';
session_start();

// Fetch total enrolled students
$totalStudents = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM instructor_student_load WHERE status = 'approved'");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalStudents = $row['total'];
}
$stmt->close();

// Fetch total courses
$totalCourses = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM courses");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalCourses = $row['total'];
}
$stmt->close();

// Fetch list of courses with enrolled students
$courses = [];
$stmt = $conn->prepare("
        SELECT 
            c.courseCode, 
            c.courseName, 
            CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, 
            ic.instructor_courseID,
            cl.classID,
            cl.section
        FROM 
            instructor_courses ic 
        JOIN 
            courses c ON ic.courseID = c.courseID 
        JOIN 
            users i ON ic.instructorID = i.userID 
        JOIN 
            class cl ON ic.classID = cl.classID
        WHERE 
            ic.instructor_courseID IN (
                SELECT instructor_courseID 
                FROM instructor_student_load 
                WHERE status = 'approved'
            )
        GROUP BY 
            ic.instructor_courseID
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    $stmt->close();

?>

<div class="home-content">
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Dashboard</h2>
            <div class="dashboard-container">
                <div class="cards-container">
                    <div class="card">
                        <i class="ti ti-users-group"></i>
                        <span>Total Enrolled Students</span>
                        <span class="totalCount">
                            <?= htmlspecialchars($totalStudents); ?>
                        </span>
                    </div>
                    <div class="card">
                        <i class="ti ti-books"></i>
                        <span>Total courses</span>
                        <span class="totalCount">
                            <?= htmlspecialchars($totalCourses); ?>
                        </span>
                    </div>
                </div>

                <div class="second_header">
                    <h3>Courses w/ List of students...</h3>
                    <div class="underline"></div>
                </div>
                <div class="course-card-container">
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card-form">
                            <input type="hidden" name="instructor_courseID" value="<?= $course['instructor_courseID'] ?>">
                            <div class="course-card">
                                <div class="course-card-header">
                                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                                    <p class="card-tag"><?= htmlspecialchars($course['section']) ?></p>
                                </div>
                                <div class="card-details">
                                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                                    <p><span>Professor:</span> <?= htmlspecialchars($course['Instructor_Name']) ?></p>

                                    <div class="card-footer">
                                        <button type="button" class="home-contentBtn btn-drk-bg" id="studentsEnrolled">Enrolled Students</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- MODAL -->
                 <div class="overlay" id="loadingOverlay">
                    <div class="popup-box">
                        <div class="popup-box-content" id="studentModal">
                            
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>