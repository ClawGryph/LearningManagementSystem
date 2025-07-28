<?php
    include '../db.php';

    session_start();
    $courseID = $_SESSION['courseID'] ?? null;

    if (!$courseID) {
        die("No course ID in session.");
    }else{
        $stmt = $conn->prepare("SELECT CONCAT(courseCode, ' - ', courseName) AS class_name FROM courses WHERE courseID = ?");
        $stmt->bind_param("i", $courseID);
        $stmt->execute();
        $stmt->bind_result($courseName);
        $stmt->fetch();
        $stmt->close();
    }
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
                        <span><!-- NUMBER OF COMPLETED TASK -->1</span>
                        <span>Completed Task</span>
                    </div>
                    <div>
                        <span><!-- NUMBER OF INCOMPLETE TASK -->1</span>
                        <span>Incomplete Task</span>
                    </div>
                    <div>
                        <span><!-- NUMBER OF OVERDUE TASK -->1</span>
                        <span>Overdue Task</span>
                    </div>
                    <div>
                        <span><!-- TOTAL TASK -->1</span>
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