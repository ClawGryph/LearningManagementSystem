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
        WHERE isl.studentID = ? AND isl.status = 'approved';
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
    <div class="content-container">
        <div class="first-page">
            <h2>Subjects</h2>
            <div class="course-card-container">
                <div class="course-card-form">
                    <button class="course-card joinBtn" id="joinButton"><i class="fa-solid fa-plus"></i> Join class</button>
                </div>
                <?php foreach ($courses as $course): ?>
                        <form action="student-subject-landingpage.php" method="POST" class="course-card-form">
                            <input type="hidden" name="instructor_courseID" value="<?= $course['instructor_courseID'] ?>">
                            <button type="submit" class="course-card">
                                <div class="course-card-header">
                                    <h3><?= htmlspecialchars($course['courseCode']) ?></h3>
                                </div>
                                <div class="card-details">
                                    <h4><?= htmlspecialchars($course['courseName']) ?></h4>
                                    <p><span>Professor:</span> <?= htmlspecialchars($course['Instructor_Name']) ?></p>
                                </div>
                            </button>
                        </form>
                <?php endforeach; ?>
            </div>

            <!-- JOIN CLASS -->
             <div class="overlay" id="loadingOverlay">
                <div class="popup-box" id="classCode">
                    <div class="popup-box-content">
                        <div class="page-header">
                            <h2>Enter class code...</h2>
                            <i class="fa-solid fa-xmark" id="closeBtn"></i>
                        </div>
                        <form class="code-content">
                            <div class="inputGroup">
                                <input type="text" id="inputCode" name="inputCode" maxlength="10">
                                <label for="inputCode">Class Code</label>
                            </div>
                            <span id="searchMessage"></span>
                            <button type="button" class="home-contentBtn btn-drk-bg" id="submitCode">Confirm</button>
                        </form>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>