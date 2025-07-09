<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="sidebar">
        <div class="logo-details">
            <i class="fa-solid fa-school-flag"></i>
            <span class="logo_name">CTU - LMS</span>
        </div>
        <ul class="nav-links">
            <li>
                <div class="icon-link" >
                    <a href="">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span class="link_name">Courses</span>
                    </a>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <span class="sub-menu-header">Courses</span>
                    <li><a href="#" data-content="admin-create-courses.php">Create Courses</a></li>
                    <li><a href="#" data-content="admin-instructor-courses.php">Add Instructor to Courses</a></li>
                </ul>
            </li>
            <li>
                <a href="#" data-content="admin-lm-lists">
                    <i class="fa-solid fa-book-open"></i>
                    <span class="link_name">Learning Materials Lists</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="admin-lm-lists" class="link_name">Learning Materials Lists</a></li>
                </ul>
            </li>
            <li>
                <a href="#" data-content="admin-notification">
                    <i class="fa-solid fa-bell"></i>
                    <span class="link_name">Notification</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a href="#" data-content="admin-notification" class="link_name">Notification</a></li> 
                </ul>
            </li>
            <li>
                <div class="profile-details">
                    <div class="profile-content">
                        <img src="../images/profile.png" alt="empty profile image">
                    </div>
                    <div class="name-job">
                        <div class="profile_name">Admin</div>
                        <div class="job">Administrator</div>
                    </div>
                    <a href="../index.php"><i class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            </li>
        </ul>
    </nav>
    <main class="home-section">
        <div class="home-content">
            <i class="fa-solid fa-bars"></i>
            <span class="menu-text">Drop Down Sidebar</span>
        </div>
    </main>
    <script src="../js/linkView.js"></script>
</body>
</html>