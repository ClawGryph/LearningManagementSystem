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
                <h2>Learning Materials</h2>
                <button type="submit" class="home-contentBtn btn-accent-bg" id="addMaterialsToClass"><i class="fa-solid fa-circle-plus"></i>Add materials to class</button>
            </div>
            <div class="class-management-container">
                <div class="class-list-container">

                    <!-- ASSIGNMENT TABLE -->
                    <div class="table-container">
                        <table class="table-content" id="materialsTable">
                            <thead>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created at</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                    include '../db.php';

                                    $stmt = $conn->prepare("SELECT * FROM course_learningmaterials");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-lm-id='{$row['course_lmID']}'>
                                                    <td>{$row['name']}</td>
                                                    <td>{$row['description']}</td>
                                                    <td>{$row['uploaded_at']}</td>
                                                    <td><div class='statusGroup {$row["status"]}'>{$row['status']}</div></td>
                                                    <td>
                                                        <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                        <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No learning materials found.</td></tr>";
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CREATE ASSIGNMENT -->
                <div class="create-class-container">
                    <form class="form-content" action="../action/addNewMaterials.php" method="POST" enctype="multipart/form-data">
                        <h2>Upload Materials</h2>
                            <label for="">File</label>

                        <div class="assignmentFile">
                            <!-- Display the selected file name -->
                            <p id="fileNameDisplay">No file chosen</p>

                            <!-- Hidden file input -->
                            <input type="file" name="assignmentFile" id="assignmentFile" accept=".doc,.docx,.pdf,.ppt,.pptx" style="display: none;" required>

                            <!-- Button to trigger the file input -->
                            <button type="button" onclick="document.getElementById('assignmentFile').click();">
                                Choose File
                            </button>
                        </div>
                        <label class="divider-label" for="">Or</label>
                        <label for="">YouTube Video Link</label>
                        <div>
                            <input type="url" name="youtubeUrl" placeholder="https://www.youtube.com/watch?v=..." pattern="https?://.*" />
                        </div>
                       
                        <label for="">Learning Material</label>
                        <div>
                            <input type="text" name="materialTitle" placeholder="Name" required>
                        </div>
                        <div>
                            <input type="text" name="materialDescription" placeholder="Description" required>
                        </div>
                       
                        <button type="submit" class="home-contentBtn btn-accent-bg">Upload</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECOND PAGE -->
        <div class="second-page hiddenPage" id="materialModal">
            <a href="#" data-content="instructor-upload-lm.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form class="quizzes" action="../action/addMaterialsToClass.php" method="POST">
                <div class="page-header">
                    <div class="inputNoToggle">
                        <!-- DROPDOWN LIST TO ALL QUIZZES THAT ARE NOT YET IN DEADLINE -->
                        <label for="materialSelect">Select a material:</label>
                        <select name="course_lmID" id="materialSelect" required>
                            <option value="" disabled selected>-- Choose a material --</option>
                            <?php
                            include '../db.php';
                            $query = "SELECT course_lmID, name, status FROM course_learningmaterials WHERE status = 'pending'";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['course_lmID'] . '">' . htmlspecialchars($row['name']) . '</option>';
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