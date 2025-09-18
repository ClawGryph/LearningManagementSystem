<?php
include '../db.php';

$query = $conn->query("SELECT CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name, 
       CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, 
       clm.name, clm.file_path, clm.youtube_url, 
       lma.lmID
        FROM learningmaterials_author lma 
        JOIN instructor_courses ic ON lma.instructor_courseID = ic.instructor_courseID 
        JOIN users i ON ic.instructorID = i.userID 
        JOIN courses c ON ic.courseID = c.courseID 
        JOIN course_learningmaterials clm ON lma.course_lmID = clm.course_lmID 
        WHERE clm.status = 'pending';");
?>

<div class="home-content">
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <div class="page-header">
                <h2>Learning materials to be approved</h2>
                <div class="clock">
                    <span id="hr">00</span>
                    <span>:</span>
                    <span id="min">00</span>
                    <span>:</span>
                    <span id="sec">00</span>
                </div>
            </div>
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
                                <td>
                                    <?php if (!empty($row['file_path'])): ?>
                                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="home-contentBtn lm-link btn-light-bg">Preview File</a><br>
                                    <?php endif; ?>

                                    <?php if (!empty($row['youtube_url'])): ?>
                                        <a href="<?= htmlspecialchars($row['youtube_url']) ?>" target="_blank" class="home-contentBtn lm-link btn-light-bg">Watch Video</a>
                                    <?php endif; ?>

                                    <?php if (empty($row['file_path']) && empty($row['youtube_url'])): ?>
                                        <span>No preview available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="home-contentBtn approve-btn btn-accent-bg" data-id="<?php echo $row['lmID']; ?>">Approve</button>
                                    <button type="button" class="home-contentBtn reject-btn btn-drk-bg" data-id="<?php echo $row['lmID']; ?>">Reject</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <p>No pending files.</p>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>