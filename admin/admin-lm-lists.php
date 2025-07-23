<?php
include '../db.php';

$query = $conn->query("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, clm.name, clm.file_path, lma.lmID
FROM learningmaterials_author lma 
JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
JOIN users i ON ic.instructorID = i.userID 
JOIN courses c ON ic.courseID = c.courseID 
JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID 
WHERE clm.status = 'pending';");
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Learning materials to be approved</h2>
            <div class="table-container">
                <?php if ($query->num_rows > 0): ?>
                <table class="table-content">
                    <thead>
                        <tr>
                            <th>Instructor Name</th>
                            <th>Course Name</th>
                            <th>File Name</th>
                            <th>Preview</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body lm-lists">
                        <?php while ($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Instructor_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php if (!empty($row['file_path'])): ?>
                                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="home-contentBtn lm-link btn-light-bg">Preview</a>
                                    <?php else: ?>
                                        <span>No file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="home-contentBtn approve-btn btn-accent-bg" data-id="<?php echo $row['lmID']; ?>">Approve</button>
                                    <button type="button" class="home-contentBtn reject-btn btn-drk-bg" data-id="<?php echo $row['lmID']; ?>">Reject</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p>No pending files.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>