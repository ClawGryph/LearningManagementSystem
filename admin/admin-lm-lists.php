<?php
include '../db.php';

$query = $conn->query("SELECT 
                        CONCAT(i.firstName, ' ', i.lastName) AS Instructor_Name,
                        c.courseName,
                        clm.course_lmID,
                        clm.file_name,
                        clm.uploaded_at
                    FROM course_learningmaterials clm
                    JOIN users i ON i.userID = clm.instructorID
                    JOIN courses c ON c.courseID = clm.courseID
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
                            <th>Date and Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php while ($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Instructor_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['courseName']); ?></td>
                                <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                                <td><?php echo date("F j, Y g:i A", strtotime($row['uploaded_at'])) ?></td>
                                <td>
                                    <button type="button" class="home-contentBtn approve-btn btn-accent-bg" data-id="<?php echo $row['course_lmID']; ?>">Approve</button>
                                    <button type="button" class="home-contentBtn reject-btn btn-drk-bg" data-id="<?php echo $row['course_lmID']; ?>">Reject</button>
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