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
<div>
    <div>
        <!-- FIRST PAGE -->
        <div>
            <h2>Learning materials to be approved</h2>
            <div>
                <?php if ($query->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Instructor Name</th>
                            <th>Course Name</th>
                            <th>File Name</th>
                            <th>Date and Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Instructor_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['courseName']); ?></td>
                                <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                                <td><?php echo date("F j, Y g:i A", strtotime($row['uploaded_at'])) ?></td>
                                <td>
                                    <button type="button" class="approve-btn" data-id="<?php echo $row['course_lmID']; ?>">Approve</button>
                                    <button type="button" class="reject-btn" data-id="<?php echo $row['course_lmID']; ?>">Reject</button>
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