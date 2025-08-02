<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <h2 id="courseTitle"></h2>
            <div>
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by student's name..." autocomplete="off">
                        <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    </div>
                    <div id="suggestions" class="suggestions-list"></div>
                    <div id="searchMessage" class="search-message"></div>
                </div>

                <div class="card-tasks"> 
                    <!-- Cards -->
                    <div>
                        <span id="completedCount">0</span>
                        <span>Completed Task</span>
                    </div>
                    <div>
                        <span id="incompleteCount">0</span>
                        <span>Incomplete Task</span>
                    </div>
                    <div>
                        <span id="overdueCount">0</span>
                        <span>Overdue Task</span>
                    </div>
                    <div>
                        <span id="totalCount">0</span>
                        <span>Total Task</span>
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
                                <option value="all">All (Quiz + Activity + Assignment)</option>
                                <option value="quiz">Quiz Only</option>
                                <option value="activity">Activity Only</option>
                                <option value="assignment">Assignment Only</option>
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
        </div>
    </div>
</div>