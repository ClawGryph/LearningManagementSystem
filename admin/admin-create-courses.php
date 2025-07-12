<?php
include '../db.php';
$instructors = [];

$query = $conn->query("SELECT userID, CONCAT(firstName, ' ', lastName) AS fullName FROM users WHERE role = 'instructor'");
while ($row = $query->fetch_assoc()) {
    $instructors[] = $row;
}
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
                <h2>Instructor Subjects</h2>
                <button type="submit" id="coursePage" class="home-contentBtn btn-accent-bg"><i class="fa-solid fa-circle-plus"></i>Create new course</button>
            </div>
            <div class="table-container">
                <table class="table-content" id="courseTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php
                        include '../db.php';

                        $stmt = $conn->prepare("SELECT c.courseCode, c.courseName, CONCAT(i.firstName, ' ', i.lastName) AS 'Instructors_Name', ic.code, ic.instructor_courseID from instructor_courses ic JOIN courses c ON ic.courseID = c.courseID JOIN users i ON ic.instructorID = i.userID;");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr data-instructor_course-id='{$row['instructor_courseID']}'>
                                        <td>{$count}</td>
                                        <td>{$row['courseCode']}</td>
                                        <td>{$row['courseName']}</td>
                                        <td>{$row['Instructors_Name']}</td>
                                        <td>{$row['code']}</td>
                                        <td>
                                            <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                            <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                        </td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No courses found.</td></tr>";
                        }

                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECOND PAGE !-->
        <div class="second-page" id="courseModal">
            <div class="page-header">
                <a href="#" data-content="admin-create-courses.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
                <h2>Create New Course</h2>
            </div>
            <form action="../action/addNewCourse.php" class="form-content" method="POST">
                <div>
                    <i class="fa-solid fa-book"></i>
                    <input type="text" id="courseCode" name="courseCode" placeholder="Enter course code" required>
                </div>
                <div>
                    <i class="fa-solid fa-keyboard"></i>
                    <input type="text" id="courseName" name="courseName" placeholder="Enter course name" required>
                </div>
                <button type="submit" id="addNewCourse" class="home-contentBtn btn-accent-bg">Create Course</button>
            </form>
        </div>
    </div>
    <script>
        // Embed instructor data into JS
        window.instructorList = <?= json_encode($instructors) ?>;
    </script>
</div>