<?php
    include '../db.php';
    session_start();

    $courseID = $_SESSION['courseID'];

    $query = $conn->query("SELECT isl.instructor_student_loadID AS request_id, isl.status, isl.request_date, isl.decision_date,
           CONCAT(s.firstName, ' ', s.lastName) AS Student_name,
           s.profileImage,
           CONCAT(c.courseCode, ' - ', c.courseName) AS Course_Name, 
           cl.section
    FROM instructor_student_load isl
    JOIN users s ON isl.studentID = s.userID
    JOIN instructor_courses ic ON isl.instructor_courseID = ic.instructor_courseID
    JOIN courses c ON ic.courseID = c.courseID
    JOIN class cl ON ic.classID = cl.classID
    WHERE isl.status = 'pending' AND ic.courseID = $courseID
    ORDER BY isl.request_date DESC");
?>

<div class="home-content">
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <h2>Enrolees</h2>
            <div class="table-container">
                <?php if ($query->num_rows > 0): ?>
                <table class="table-content">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Student Name</th>
                            <th>Course Name</th>
                            <th>Section</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body lm-lists">
                        <?php while ($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['profileImage'])): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($row['profileImage']); ?>" 
                                                    alt="Profile" 
                                                    class="profile-img">
                                            <?php else: ?>
                                                <img src="../uploads/default-profile.png" 
                                                    alt="Default Profile" 
                                                    class="profile-img">
                                            <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['Student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                                <td>
                                    <button type="button" class="home-contentBtn approve-btn btn-accent-bg" data-id="<?php echo $row['request_id']; ?>">Approve</button>
                                    <button type="button" class="home-contentBtn reject-btn btn-drk-bg" data-id="<?php echo $row['request_id']; ?>">Reject</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <td colspan="6">No request of enrolees.</td>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>