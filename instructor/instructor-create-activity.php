<?php
session_start();
include '../db.php';

$languages = [];

$query = $conn->query("SELECT activityID, language FROM programming_activity");
while($row = $query->fetch_assoc()){
    $languages[] = $row;
}
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <div class="page-header">
                <h2>Programming Activity</h2>
                <button type="submit" class="home-contentBtn btn-accent-bg" id="addActivityToClass"><i class="fa-solid fa-circle-plus"></i>Add activity to class</button>
            </div>
            <div class="class-management-container">
                <div class="activity-list-container">

                    <!-- ASSIGNMENT TABLE -->
                    <div class="table-container">
                        <table class="table-content" id="activityTable">
                            <thead>
                                <th>Title</th>
                                <th>Language</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                    include '../db.php';
                                    $instructorID = $_SESSION['user_id'];

                                    $stmt = $conn->prepare("SELECT activityID, instructor_ID, title, language, deadline FROM programming_activity WHERE instructor_id = ?");
                                    $stmt->bind_param("i", $instructorID);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-activity-id='{$row['activityID']}'>
                                                    <td>{$row['title']}</td>
                                                    <td>{$row['language']}</td>
                                                    <td>{$row['deadline']}</td>
                                                    <td>
                                                        <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                        <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No activity found.</td></tr>";
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CREATE ASSIGNMENT -->
                <div class="create-activity-container">
                    <form class="form-content" action="../action/addNewActivity.php" method="POST" enctype="multipart/form-data">
                        <h2>Create Programming Activity</h2>
                            <label for="">File</label>
                       
                        <label>Language</label>
                        <select name="language" required>
                            <option value="" disabled selected>-- SELECT LANGUAGE --</option>
                            <option value="c">C</option>
                        </select>

                        <label>Title</label>
                        <div>
                            <input type="text" name="title" required>
                        </div>

                        <label>Instructions</label>
                        <div>
                            <textarea name="instructions" required></textarea>
                        </div>

                        <label>Expected Output</label>
                        <div>
                            <textarea name="expected_output" required></textarea>
                        </div>

                        <label>Deadline</label>
                        <div>
                            <input type="datetime-local" name="activityDeadline" required>
                        </div>
                       
                        <button type="submit" class="home-contentBtn btn-accent-bg">Create</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECOND PAGE -->
        <div class="second-page" id="activityModal">
            <a href="#" data-content="instructor-create-activity.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form class="quizzes" action="../action/addActivityToClass.php" method="POST">
                <div class="page-header">
                    <div class="inputNoToggle">
                        <!-- DROPDOWN LIST TO ALL QUIZZES THAT ARE NOT YET IN DEADLINE -->
                        <label for="assignmentSelect">Select an activity:</label>
                        <select name="activityID" id="activitySelect" required>
                            <option value="" disabled selected>-- Choose an activity --</option>
                            <?php
                            include '../db.php';
                            $instructorID = $_SESSION['user_id'];

                            date_default_timezone_set('Asia/Manila');
                            $now = date('Y-m-d H:i:s');
                            $query = "SELECT activityID, instructor_ID, title, deadline FROM programming_activity WHERE deadline >= ? AND instructor_ID = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("si", $now, $instructorID);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['activityID'] . '">' . htmlspecialchars($row['title']) . ' (Deadline: ' . date('M d, Y H:i', strtotime($row['deadline'])) . ')</option>';
                            }

                            $stmt->close();
                            ?>
                        </select>
                    </div>
                    <div class="inputGroup">
                        <input type="number" name="activityTime" min="1" max="60" required>
                        <label for="activityTime">Activity Timelimit (in minutes)</label>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table-content">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Class</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <!-- RADIO BUTTON IN EACH CLASS AND SUBJECT -->
                            <?php
                            include '../db.php';
                            
                            $instructorID = $_SESSION['user_id'];
                            
                            $result = $conn->prepare("SELECT cl.year, cl.section, c.courseCode, c.courseName, ic.instructor_courseID
                                                    FROM instructor_courses ic
                                                    JOIN class cl ON ic.classID = cl.classID
                                                    JOIN courses c ON ic.courseID = c.courseID
                                                    WHERE instructorID = ?");
                            $result->bind_param("i", $instructorID);
                            $result->execute();
                            $stmt = $result->get_result();
                            
                            while ($row = $stmt->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td><input type="radio" name="instructorCourseID" value="' . $row['instructor_courseID'] . '" required></td>';
                                echo '<td>' . htmlspecialchars($row['year']) . '-' . htmlspecialchars($row['section']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['courseCode']) . '-' . htmlspecialchars($row['courseName']) . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="home-contentBtn-container">
                    <button type="submit" id="submitAssignment" class="home-contentBtn btn-accent-bg">Add</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        window.languagelist = <?= json_encode($languages) ?>;
    </script>
</div>