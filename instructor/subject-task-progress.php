<?php
    include '../action/get-task-statistics.php';
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <h2><?= htmlspecialchars($courseName) ?></h2>
            <div>
                <div class="search-container">
                    <!-- SEARCH BAR -->
                    <input type="text" id="searchInput" placeholder="Search by students name...">
                    <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                </div>
                <div class="card-tasks">
                    <!-- Cards -->
                    <div>
                        <span><?= $taskCounts['completed'] ?></span>
                        <span>Completed Task</span>
                    </div>
                    <div>
                        <span><?= $taskCounts['incomplete'] ?></span>
                        <span>Incomplete Task</span>
                    </div>
                    <div>
                        <span><?= $taskCounts['overdue'] ?></span>
                        <span>Overdue Task</span>
                    </div>
                    <div>
                        <span><?= $taskCounts['total'] ?></span>
                        <span>Total Task</span>
                    </div>
                </div>
                <div>
                    <!-- Bars -->
                </div>
            </div>
        </div>
    </div>
</div>