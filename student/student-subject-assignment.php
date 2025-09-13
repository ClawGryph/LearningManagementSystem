<?php
include '../db.php';
session_start();

$studentID = $_SESSION['user_id']; // Logged-in student ID
$instructorCourseID = $_SESSION['instructor_courseID']; // Stored in session

$sql = "
SELECT 
    a.assignmentID,
    a.title,
    a.description,
    a.file_path,
    a.deadline,
    sa.status
FROM instructor_student_load isl
JOIN assessment_author aa
    ON aa.instructor_courseID = isl.instructor_courseID
JOIN assignment a
    ON a.assignmentID = aa.assessment_refID
JOIN student_assessments sa
    ON sa.assessment_authorID = aa.assessment_authorID
    AND sa.student_id = isl.studentID
WHERE isl.studentID = ?
  AND isl.instructor_courseID = ?
  AND aa.assessment_type = 'assignment'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentID, $instructorCourseID);
$stmt->execute();
$result = $stmt->get_result();

$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <h2>Assignment</h2>
                <div class="class-list-container">
                    <div class="table-container">
                        <table class="table-content">
                            <thead>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Due Date</th>
                                <th>Action</th>
                                <th>Status</th>
                            </thead>
                            <tbody class="table-body">
                                <?php if (!empty($assignments)): ?>
                                    <?php foreach ($assignments as $ass): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ass['title']) ?></td>
                                            <td><?= htmlspecialchars($ass['description']) ?></td>
                                            <td><?= htmlspecialchars($ass['deadline']) ?></td>
                                            <td>
                                                <a href="../uploads/assignments/<?= htmlspecialchars($ass['file_path']) ?>" download class="home-contentBtn btn-save btn-accent-bg" title="Save"><i class="fa-solid fa-download"></i></a>
                                            </td>
                                            <td>
                                                <div class="statusGroup <?= strtolower(str_replace(' ', '-', htmlspecialchars($ass['status']))) ?>">
                                                    <?= htmlspecialchars($ass['status']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No assignments found.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                        </table>
                    </div>

                <div class="create-class-container">
                    <form action="../action/submitAssignment.php" class="form-content" method="POST" enctype="multipart/form-data">
                    <h2>Submit Assignment</h2>

                    <label for="assignmentSelect">Select Assignment</label>
                        <select name="assignmentID" id="assignmentSelect" required>
                            <option value="" disabled selected>-- Choose an Assignment --</option>
                            <?php foreach ($assignments as $ass): ?>
                                <option value="<?= htmlspecialchars($ass['assignmentID']) ?>">
                                    <?= htmlspecialchars($ass['title']) ?> (Due: <?= htmlspecialchars($ass['deadline']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <div class="assignmentFile">
                        <!-- Display the selected file name -->
                        <p id="fileNameDisplay">No file chosen</p>

                        <!-- Hidden file input -->
                        <input type="file" name="assignment_file" id="assignmentFile" style="display: none;" required>

                        <!-- Button to trigger the file input -->
                        <button type="button" onclick="document.getElementById('assignmentFile').click();">
                            Choose File
                        </button>
                    </div>
                    
                    <button type="submit" class="home-contentBtn btn-accent-bg">Submit</button>
                </form>

                </div>
        </div>
    </div>
</div>