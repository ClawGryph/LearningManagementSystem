<?php
include '../db.php';

// Fetch all instructors
$stmt = $conn->prepare("SELECT userID, firstName, lastName FROM users WHERE role = 'instructor'");
$stmt->execute();
$result = $stmt->get_result();

// Fetch all courses
$courseStmt = $conn->prepare("SELECT courseID, courseCode, courseName FROM courses");
$courseStmt->execute();
$courses = $courseStmt->get_result();
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Add Instructor to the course</h2>
            <form method="POST" class="form-content form-widthpercent60" action="../action/assignCourse.php">
                <h3>Select Instructor</h3>
                <!-- List of instructors will be populated here -->
                <select name="instructor_id" required>
                    <option value="">-- Select Instructor --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['userID']) ?>">
                            <?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <h3>Select Course</h3>
                <!-- List of courses will be populated here -->
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <div>
                        <input type="radio" name="course_id" value="<?= htmlspecialchars($course['courseID']) ?>" id="course_<?= $course['courseID'] ?>" required>
                        <label for="course_<?= $course['courseID'] ?>">
                            <?= htmlspecialchars($course['courseCode']) ?> - <?= htmlspecialchars($course['courseName']) ?>
                        </label>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="home-contentBtn btn-accent-bg" name="add_instructor">Add Instructor</button>
            </form>
        </div>
    </div>
</div>