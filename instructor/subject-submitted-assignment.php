<?php
include '../action/get-course-title.php';
?>

<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <div class="page-header">
                <h2><?php echo htmlspecialchars($courseName ?? 'Unknown Course'); ?></h2>
                <h3>Submitted Assignment</h3>
            </div>
            <div>
                <form action="">
                    <table>
                        <thead>
                            <th>Submitted by</th>
                            <th>Date Submitted</th>
                            <th>Download</th>
                            <th>Score</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>