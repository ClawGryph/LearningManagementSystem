<?php
include '../db.php';

// Fetch all pending learning materials
$notifQuery = $conn->query("SELECT 
    CONCAT(u.firstName, ' ', u.lastName) AS instructor_name,
    c.courseName,
    lm.uploaded_at
FROM course_learningmaterials lm
JOIN users u ON u.userID = lm.instructorID
JOIN courses c ON c.courseID = lm.courseID
WHERE lm.status = 'pending'
ORDER BY lm.uploaded_at DESC
");
?>

<div class="home-content">
    <div>
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <div>
                <h2>Welcome, Admin</h2>
            </div>
            <div>
                <div>
                    <button type="submit"><i class="fa-regular fa-circle-check"></i>Read</button>
                    <input type="radio" id="checkAll" name="choices" value="checkAll">
                    <label for="checkAll">Check All</label>
                </div>
                <?php if ($notifQuery->num_rows > 0): ?>
                <?php while ($notif = $notifQuery->fetch_assoc()): ?>
                    <div class="notification-item">
                        <p>
                            <span><strong><?= htmlspecialchars($notif['instructor_name']) ?></strong></span>
                            added a course material to the course 
                            <span><strong><?= htmlspecialchars($notif['courseName']) ?></strong></span>
                        </p>
                        <p>
                            <span><i class="fa-solid fa-calendar-days"></i>
                                <?= date("F j, Y g:i A", strtotime($notif['uploaded_at'])) ?>
                            </span>
                        </p>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <p>No new notifications.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>