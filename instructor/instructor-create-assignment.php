<?php
session_start();
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
                <h2>Assignment</h2>
                <button type="submit" class="home-contentBtn btn-accent-bg" id="addAssignmentToClass"><i class="fa-solid fa-circle-plus"></i>Add assignment to class</button>
            </div>
            <div class="class-management-container">
                <div class="class-list-container">

                    <!-- ASSIGNMENT TABLE -->
                    <div class="table-container">
                        <table class="table-content" id="assignmentTable">
                            <thead>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                    include '../db.php';

                                    $stmt = $conn->prepare("SELECT * FROM assignment");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-assignment-id='{$row['assignmentID']}'>
                                                    <td>{$row['title']}</td>
                                                    <td>{$row['description']}</td>
                                                    <td>{$row['deadline']}</td>
                                                    <td>
                                                        <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                        <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No course found.</td></tr>";
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CREATE ASSIGNMENT -->
                <div class="create-class-container">
                    <form class="form-content" action="../action/addNewAssignment.php" method="POST" enctype="multipart/form-data">
                        <h2>Create Assignment</h2>
                            <label for="">File</label>

                        <div class="assignmentFile">
                            <!-- Display the selected file name -->
                            <p id="fileNameDisplay">No file chosen</p>

                            <!-- Hidden file input -->
                            <input type="file" name="assignmentFile" id="assignmentFile" style="display: none;" required>

                            <!-- Button to trigger the file input -->
                            <button type="button" onclick="document.getElementById('assignmentFile').click();">
                                Choose File
                            </button>
                        </div>
                       
                        <label for="">Assignment</label>
                        <div>
                            <input type="text" name="assignmentTitle" placeholder="Name" required>
                        </div>
                        <div>
                            <input type="text" name="assignmentDescription" placeholder="Description" required>
                        </div>
                        <div>
                            <input type="datetime-local" name="assignmentDeadline" placeholder="Deadline" required>
                        </div>
                       
                        <button type="submit" class="home-contentBtn btn-accent-bg">Create</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECOND PAGE -->
        <div class="second-page" id="assignmentModal">
            <a href="#" data-content="instructor-create-assignment.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form class="quizzes" action="../action/addAssignmentToClass.php" method="POST">
                <div class="page-header">
                    <div class="inputNoToggle">
                        <!-- DROPDOWN LIST TO ALL QUIZZES THAT ARE NOT YET IN DEADLINE -->
                        <label for="assignmentSelect">Select a assignment:</label>
                        <select name="assignmentID" id="assignmentSelect" required>
                            <option value="">-- Choose an assignment --</option>
                            <?php
                            include '../db.php';
                            date_default_timezone_set('Asia/Manila');
                            $now = date('Y-m-d H:i:s');
                            $query = "SELECT assignmentID, title, deadline FROM assignment WHERE deadline >= ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("s", $now);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['assignmentID'] . '">' . htmlspecialchars($row['title']) . ' (Deadline: ' . date('M d, Y H:i', strtotime($row['deadline'])) . ')</option>';
                            }

                            $stmt->close();
                            ?>
                        </select>
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
</div>