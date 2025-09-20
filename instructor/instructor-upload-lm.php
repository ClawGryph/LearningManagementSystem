<?php
session_start();
?>

<div class="home-content">
    <div class="content-container">
        <!-- FIRST PAGE -->
        <div class="first-page">
            <div class="page-header">
                <h2>Learning Materials</h2>
                <div class="clock">
                    <span id="hr">00</span>
                    <span>:</span>
                    <span id="min">00</span>
                    <span>:</span>
                    <span id="sec">00</span>
                </div>
                <button type="submit" class="home-contentBtn btn-accent-bg" id="addMaterialsToClass"><i class="fa-solid fa-circle-plus"></i>Add materials to class</button>
            </div>
                <div class="class-list-container">

                    <!-- ASSIGNMENT TABLE -->
                    <div class="table-container">
                        <table class="table-content" id="materialsTable">
                            <thead>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created at</th>
                                <th>Status</th>
                                <th>Section</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                    include '../db.php';

                                    $instructorID = $_SESSION['user_id'];

                                    $stmt = $conn->prepare("SELECT lm.course_lmID, lm.name, lm.description, la.request_date, lm.status, cl.section,
                                                            CASE 
                                                                WHEN ic.classID IS NULL THEN 'not yet assigned'
                                                                ELSE 'pending'
                                                            END AS display_status
                                                            FROM course_learningmaterials lm
                                                            LEFT JOIN learningmaterials_author la ON lm.course_lmID = la.course_lmID
                                                            LEFT JOIN instructor_courses ic ON la.instructor_courseID = ic.instructor_courseID
                                                            LEFT JOIN class cl ON ic.classID = cl.classID
                                                            WHERE instructor_ID = ?;");
                                    $stmt->bind_param("i", $instructorID);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $status = $row['status'];

                                            if (empty($row['section'])) {
                                                $status = "not yet assigned";
                                            }
                                            echo "<tr data-lm-id='{$row['course_lmID']}'>
                                                    <td>{$row['name']}</td>
                                                    <td>{$row['description']}</td>
                                                    <td>" . date("F j, Y g:i A", strtotime($row['request_date'])) . "</td>
                                                    <td><div class='statusGroup {$status}'>{$status}</div></td>
                                                    <td>{$row['section']}</td>
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

                <!-- CREATE ASSIGNMENT -->
                <div class="create-class-container">
                    <form class="form-content" id="upload-lm-form" action="../action/addNewMaterials.php" method="POST" enctype="multipart/form-data">
                        <h2>Upload Materials</h2>
                            <label for="">File</label>

                        <div class="assignmentFile">
                            <!-- Display the selected file name -->
                            <p id="fileNameDisplay">No file chosen</p>

                            <!-- Hidden file input -->
                            <input type="file" name="assignmentFile" id="assignmentFile" accept=".doc,.docx,.pdf,.ppt,.pptx" style="display: none;">

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

                            $instructorID = $_SESSION['user_id'];

                            $query = "SELECT course_lmID, instructor_ID, name, status FROM course_learningmaterials WHERE status = 'pending' AND instructor_ID = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $instructorID);
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