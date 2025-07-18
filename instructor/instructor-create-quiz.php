<?php
session_start();
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <!-- FIRST PAGE !-->
        <div class="first-page">
            <div class="page-header">
                <h2>Quizzes</h2>
                <div>
                    <button type="submit" id="addQuizPage" class="home-contentBtn btn-accent-bg"><i class="fa-solid fa-circle-plus"></i>Add Quiz</button>
                    <button type="submit" id="addQuizToClassPage" class="home-contentBtn btn-accent-bg"><i class="fa-solid fa-circle-plus"></i>Add quiz to class</button>
                </div>
            </div>
            <div class="table-container">
                <table class="table-content" id="quizTable">
                    <thead>
                        <tr>
                            <th>Quiz Name</th>
                            <th>Quiz Description</th>
                            <th>Deadline</th>
                            <th>Questions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php
                            include '../db.php';

                            $query = $conn->prepare("SELECT * FROM quizzes");
                            $query->execute();
                            $result = $query->get_result();

                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    echo "<tr data-quiz-id='{$row['quizID']}'>
                                            <td>{$row['title']}</td>
                                            <td>{$row['description']}</td>
                                            <td>{$row['deadline']}</td>
                                            <td><a href='#' class='view-questions' data-id={$row['quizID']}>Questions</a></td>
                                            <td>
                                                <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                            </td>
                                        </tr>";
                                }
                            }else{
                                echo "<tr><td colspan='5'>No quiz found.</td></tr>";
                            }
                        ?>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECOND PAGE !-->
        <div class="second-page" id="addQuizModal">
            <a href="#" data-content="instructor-create-quiz.php"><i class="fa-solid fa-circle-arrow-left"></i></a>

            <form id="quizForm" class="quizzes" action="../action/addNewQuiz.php" method="POST">
                <div class="page-header">
                    <h2>Create Quiz</h2>
                    <div class="inputGroup">
                        <input type="text" id="quizTitle" name="quizTitle" required maxlength="50">
                        <label for="quizTitle">Quiz title</label>
                    </div>
                    <div class="inputGroup">
                        <input type="text" id="quizDescription" name="quizDescription" required maxlength="100">
                        <label for="quizDescription">Quiz description</label>
                    </div>
                    <div class="inputNoToggle">
                        <label for="quizDeadline">Deadline:</label>
                        <input type="datetime-local" id="quizDeadline" name="quizDeadline" required>
                    </div>
                </div>

                <!-- Where all question blocks will go -->
                <div id="questionsContainer" class="questionContent">
                    <!-- First Question Block -->
                    <div class="question-block">
                        <p>Question # <span class="questionNumber">1</span></p>
                        <div class="inputNoToggle">
                            <label>Question Type:</label>
                            <select class="quizTypeSelect" name="quizTypeSelect[]">
                                <option value="multiple" selected>Multiple Choice</option>
                                <option value="identification">Identification</option>
                                <option value="truefalse">True/False</option>
                            </select>
                        </div>

                        <div class="quizInputsContainer">
                            <div class="multipleChoiceInputs">
                                <div class="inputGroup">
                                    <input type="text" name="question[]">
                                    <label for="question[]">Enter question...</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="A[]">
                                    <label for="A[]">a</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="B[]">
                                    <label for="B[]">b</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="C[]">
                                    <label for="C[]">c</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="D[]">
                                    <label for="D[]">d</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="correctAnswer[]" pattern="[a-dA-D]" maxlength="1" title="Please enter only a, b, c, or d">
                                    <label for="correctAnswer[]">Correct answer (e.g. a)</label>
                                </div>
                            </div>

                            <div class="identificationInputs" style="display:none;">
                                <div class="inputGroup">
                                    <input type="text" name="identificationQuestion[]">
                                    <label for="identificationQuestion">Enter question...</label>
                                </div>
                                <div class="inputGroup">
                                    <input type="text" name="identificationAnswer[]">
                                    <label for="identificationAnswer">Correct answer...</label>
                                </div>
                            </div>

                            <div class="trueFalseInputs" style="display:none;">
                                <div class="inputGroup">
                                    <input type="text" name="tfQuestion[]">
                                    <label for="tfQuestion">Enter question...</label>
                                </div>
                                <div class="inputNoToggle">
                                    <select name="tfAnswer[]">
                                        <option value="">Select answer</option>
                                        <option value="True">True</option>
                                        <option value="False">False</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tooltip">
                    <button type="button" class="addBtn" id="addQuestionBtn">
                        <i class="fa-solid fa-circle-plus"></i>
                    </button>
                    <span class="tooltiptext">Add Question</span>
                </div>
                <div class="home-contentBtn-container">
                    <button type="submit" class="home-contentBtn btn-accent-bg">Create Quiz</button>
                </div>
            </form>
        </div>

        <!-- THIRD PAGE !-->
        <div class="second-page" id="addQuizToClassModal">
            <a href="#" data-content="instructor-create-quiz.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form action="../action/addQuizToClass.php" class="quizzes" method="POST">
                <div class="page-header">
                    <div class="inputNoToggle">
                        <!-- DROPDOWN LIST TO ALL QUIZZES THAT ARE NOT YET IN DEADLINE -->
                        <label for="quizSelect">Select a quiz:</label>
                        <select name="quizID" id="quizSelect" required>
                            <option value="">-- Choose a quiz --</option>
                            <?php
                            include '../db.php';
                            date_default_timezone_set('Asia/Manila');
                            $now = date('Y-m-d H:i:s');
                            $query = "SELECT quizID, title, deadline FROM quizzes WHERE deadline >= ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("s", $now);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['quizID'] . '">' . htmlspecialchars($row['title']) . ' (Deadline: ' . date('M d, Y H:i', strtotime($row['deadline'])) . ')</option>';
                            }

                            $stmt->close();
                            ?>
                        </select>
                    </div>
                    <div class="inputGroup">
                        <input type="number" name="quizTime" min="1" max="60" required>
                        <label for="quizTime">Quiz Time (in minutes)</label>
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
                    <button type="submit" id="submitQuiz" class="home-contentBtn btn-accent-bg">Add</button>
                </div>
            </form>
        </div>

        <!-- QUESTIONS PAGE !-->
        <div class="second-page" id="questions">
            <div class="page-header">
                <a href="#" data-content="instructor-create-quiz.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
                <h2>Questions</h2>
            </div>
            <form id="editQuizForm" action="../action/updateQuizQuestions.php" method="POST">
                <input type="hidden" name="quizID" id="editQuizID">
                <div id="questionEditContainer">
                    <!-- Loaded questions here -->
                </div>
                <div class="home-contentBtn-container">
                    <button type="submit" id="submitCourse" class="home-contentBtn btn-accent-bg">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>