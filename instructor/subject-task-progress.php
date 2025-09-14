<?php
    include '../db.php';
    session_start();

    $courseID = $_SESSION['courseID'];
    $section = $_SESSION['classID'];
    $instructorID = $_SESSION['user_id'];

    $enrolees = [];

    $query = ("SELECT CONCAT(s.firstName, ' ', s.lastName) AS Student_Name, s.profileImage 
                            FROM instructor_courses ic 
                            JOIN instructor_student_load isl ON ic.instructor_courseID = isl.instructor_courseID 
                            JOIN users s ON isl.studentID = s.userID 
                            JOIN class cl ON ic.classID = cl.classID
                            WHERE ic.instructorID = ? AND ic.courseID = ? AND isl.status='approved' AND cl.classID = ?");
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $instructorID, $courseID, $section);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $enrolees[] = $row;
    }
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <div class="page-header">
                <h2 id="courseTitle"></h2>
                <button type="submit" class="home-contentBtn btn-accent-bg" id="showEnrolees"><i class="fa-solid fa-list-ol"></i>Students Enrolled</button>
            </div>
            <div>
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by student's name..." autocomplete="off">
                        <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    </div>
                    <div id="suggestions" class="suggestions-list"></div>
                    <div id="searchMessage" class="search-message"></div>
                </div>

                <div class="progress-section">
                    <div class="donut-chart">
                        <canvas id="quizChart"></canvas>
                        <p>Quiz</p>
                    </div>
                    <div class="donut-chart">
                        <canvas id="assignmentChart"></canvas>
                        <p>Assignment</p>
                    </div>
                    <div class="donut-chart">
                        <canvas id="activityChart"></canvas>
                        <p>Activity</p>
                    </div>
                </div>

                <!-- CHARTS -->
                <div class="charts-container">
                    <!-- Score Bar Chart -->
                    <div class="chart-card">
                        <h3>Average Scores</h3>
                        <canvas id="scoreChart"></canvas>
                    </div>

                    <!-- Task Type Donut Chart + Filter -->
                    <div class="chart-card">
                        <h3>Task Distribution</h3>
                        <div class="filter-group">
                            <label for="taskTypeFilter">Filter:</label>
                            <select id="taskTypeFilter">
                                
                            </select>
                        </div>
                        <canvas id="taskTypeDonutChart"></canvas>
                    </div>

                    <!-- Tabs Open Bar Chart -->
                    <div class="chart-card">
                        <h3>Tabs Opened Per Task</h3>
                        <canvas id="tabsOpenBarChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Task Breakdown Table -->
            <div class="task-status-filter">
                <div>
                    <label for="statusFilter">Filter by Status:</label>
                    <select id="statusFilter">
                        <option value="all">All</option>
                        <option value="completed">Completed</option>
                        <option value="incomplete">Incomplete</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>

                <div class="table-container">
                    <table class="table-content" id="studentTaskTable">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Student Name</th>
                            <th>Assessment Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Score</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                            <!-- Rows populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- STUDENTS ENROLLED LISTS -->
             <div class="overlay" id="loadingOverlay">
                <div class="popup-box" id="enrolee">
                    <div class="popup-box-content">
                        <div class="page-header">
                            <h2>Students Enrolled</h2>
                            <i class="fa-solid fa-xmark" id="closeBtn"></i>
                        </div>
                        <ol class="enrolees-list">
                            <?php foreach ($enrolees as $en): ?>
                                <li>
                                        <?php if (!empty($submission['profileImage'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($en['profileImage']); ?>" 
                                                    alt="Profile" 
                                                    class="profile-img">
                                        <?php else: ?>
                                            <img src="../uploads/default.png" 
                                                    alt="Default Profile" 
                                                    class="profile-img">
                                        <?php endif; ?> 
                                        <span><?= htmlspecialchars($en['Student_Name']) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($enrolees)): ?>
                                <span>No students are enrolled</span>
                            <?php endif; ?> 
                        </ol>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>