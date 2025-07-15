<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main class="home-section">
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
                    <button type="submit" id="coursePage" class="home-contentBtn btn-accent-bg"><i class="fa-solid fa-circle-plus"></i>Add Quiz</button>
                    <button type="submit" id="coursePage" class="home-contentBtn btn-accent-bg"><i class="fa-solid fa-circle-plus"></i>Add quiz to class</button>
                </div>
            </div>
            <div class="table-container">
                <table class="table-content" id="courseTable">
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
                        
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECOND PAGE !-->
        <div class="second-page" id="addQuizModal">
            <a href="#" data-content="instructor-create-quiz.php"><i class="fa-solid fa-circle-arrow-left"></i></a>

           <form id="quizForm" action="" method="POST">
                <div>
                    <input type="text" id="quizTitle" name="quizTitle" placeholder="Enter quiz title..." required maxlength="50">
                    <input type="text" id="quizDescription" name="quizDescription" placeholder="Enter quiz description..." required maxlength="100">
                </div>

                <!-- Where all question blocks will go -->
                <div id="questionsContainer">
                    <!-- First Question Block -->
                    <div class="question-block">
                        <p>Question # <span class="questionNumber"></span></p>
                        <label>Question Type:</label>
                        <select class="quizTypeSelect">
                            <option value="multiple" selected>Multiple Choice</option>
                            <option value="identification">Identification</option>
                            <option value="truefalse">True/False</option>
                        </select>

                        <div class="quizInputsContainer">
                            <div class="multipleChoiceInputs">
                                <input type="text" name="question[]" placeholder="Enter question..." required>
                                <input type="text" name="A[]" placeholder="a" required>
                                <input type="text" name="B[]" placeholder="b" required>
                                <input type="text" name="C[]" placeholder="c">
                                <input type="text" name="D[]" placeholder="d">
                                <input type="text" name="correctAnswer[]" placeholder="Correct answer (e.g. a)" pattern="[a-dA-D]" maxlength="1" title="Please enter only a, b, c, or d">
                            </div>

                            <div class="identificationInputs" style="display:none;">
                                <input type="text" name="identificationQuestion[]" placeholder="Enter question..." required>
                                <input type="text" name="identificationAnswer[]" placeholder="Correct answer..." required>
                            </div>

                            <div class="trueFalseInputs" style="display:none;">
                                <input type="text" name="tfQuestion[]" placeholder="Enter question..." required>
                                <select name="tfAnswer[]">
                                    <option value="">Select answer</option>
                                    <option value="True">True</option>
                                    <option value="False">False</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="addQuestionBtn">
                    <i class="fa-solid fa-circle-plus"></i>
                </button>

                <button type="submit" class="home-contentBtn btn-accent-bg">Create Quiz</button>
            </form>
        </div>

        <!-- THIRD PAGE !-->
        <div class="second-page" id="addQuizToClassModal">
            <a href="#" data-content="admin-create-courses.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form action="../action/addNewCourse.php" method="POST">
                <div>
                    <!-- DROPDOWN LIST TO ALL QUIZZES THAT ARE NOT YET IN DEADLINE -->
                </div>
                <div>
                    <input type="number" name="quizTime" placeholder="Quiz Time (in minutes)" min="1" max="60" required>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Class</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- RADIO BUTTON IN EACH CLASS AND SUBJECT -->
                    </tbody>
                </table>
                <button type="submit" id="submitQuiz" class="home-contentBtn btn-accent-bg">Add</button>
            </form>
        </div>

        <!-- QUESTIONS PAGE !-->
        <div class="second-page" id="courseModal">
            <div class="page-header">
                <a href="#" data-content="admin-create-courses.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
                <h2>Questions</h2>
            </div>
            <form action="../action/addNewCourse.php" class="form-content form-widthmin500" method="POST">
                <div><!-- DISPLAY ALL QUESTIONS OF THAT QUIZ --></div>
                <button type="submit" id="submitCourse" class="home-contentBtn btn-accent-bg">Update</button>
            </form>
        </div>
    </div>
</div>
    </main>
<script src="../js/questionToggle.js"></script>
</body>
</html>