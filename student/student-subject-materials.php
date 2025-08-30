<?php
include '../db.php';
session_start();

if (!isset($_SESSION['instructor_courseID'])) {
    die("No course selected.");
}

$instructor_courseID = $_SESSION['instructor_courseID'];

$sql = "
    SELECT CONCAT(i.firstName, ' ', i.lastName) as Instructor_Name, 
    cla.course_lmID, cla.name, cla.description, cla.file_name, 
    cla.file_path, cla.file_type, cla.file_size, cla.youtube_url, cla.uploaded_at 
    FROM learningmaterials_author lma 
    INNER JOIN course_learningmaterials cla ON lma.course_lmID = cla.course_lmID 
    INNER JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
    INNER JOIN users i ON ic.instructorID = i.userID 
    WHERE cla.status = 'approved' AND lma.instructor_courseID = ? 
    ORDER BY cla.uploaded_at DESC;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_courseID);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <h2>Learning Materials</h2>
            <div class="materials-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="material-card">
                            <div class="material-icon">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>
                            <div class="material-content">
                                <div class="material-header">
                                    <p class="material-author"><?= htmlspecialchars($row['Instructor_Name']) ?>, </p>
                                    <p class="material-note">added new material: </p>
                                    <p class="material-title"><?= htmlspecialchars($row['name']) ?></p>
                                    <p class="material-desc"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                                </div>

                                <div class="material-actions">
                                    <?php if (!empty($row['youtube_url'])): ?>
                                        <a href="<?= htmlspecialchars($row['youtube_url']) ?>" target="_blank" class="youtube-btn">
                                            <i class="fa-brands fa-youtube"></i> <span>Watch on YouTube</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($row['file_path'])): ?>
                                        <a class="download-btn" 
                                        href="<?= htmlspecialchars($row['file_path']) ?>" 
                                        download="<?= htmlspecialchars($row['file_name']) ?>">
                                            <i class="fa-solid fa-download"></i> Download <?= htmlspecialchars($row['file_name']) ?> 
                                            (<?= htmlspecialchars($row['file_type']) ?>, <?= round($row['file_size']/1024, 2) ?> KB)
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <small>📅 Uploaded: <?= htmlspecialchars($row['uploaded_at']) ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No materials available for this course.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>