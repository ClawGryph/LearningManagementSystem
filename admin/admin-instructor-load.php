<?php
include '../db.php';
$instructors = [];
$courses = [];
$classes = [];

//Fetch Instructor
$query = $conn->query("SELECT userID, CONCAT(firstName, ' ', lastName) AS fullName FROM users WHERE role = 'instructor'");
while ($row = $query->fetch_assoc()) {
    $instructors[] = $row;
}

//Fetch Courses
$courseStmt = $conn->query("SELECT * FROM courses");
while($row = $courseStmt->fetch_assoc()){
    $courses[] = $row;
}

//Fetch Classes
$classStmt = $conn->query("SELECT classID, year, section FROM class");
while($row = $classStmt->fetch_assoc()){
    $classes[] = $row;
}
?>

<div class="home-content">
    <div class="content-container">
        <div class="first-page">
            <h2>Instructor Subjects</h2>

            <div class="table-container">
                <table class="table-content" id="instructor-load-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th></th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Class</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php
                        include '../db.php';

                        $stmt = $conn->prepare("SELECT c.courseCode, c.courseName, CONCAT(i.firstName, ' ', i.lastName) AS 'Instructors_Name', i.profileImage, cl.year, cl.section, ic.code, ic.instructor_courseID from instructor_courses ic JOIN courses c ON ic.courseID = c.courseID JOIN class cl ON ic.classID = cl.classID JOIN users i ON ic.instructorID = i.userID;");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr data-instructor_course-id='{$row['instructor_courseID']}'>
                                        <td>{$count}</td> 
                                        <td><img src='../uploads/{$row['profileImage']}' alt='Profile Image' class='profile-img'></td>
                                        <td>{$row['courseCode']} - {$row['courseName']}</td>
                                        <td>{$row['Instructors_Name']}</td>
                                        <td>{$row['year']} - {$row['section']}</td>
                                        <td>{$row['code']}</td>
                                        <td>
                                            <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                            <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                        </td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No assigned courses found.</td></tr>";
                        }

                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Embed instructor data into JS
        window.courseList = <?= json_encode($courses) ?>;
        window.instructorList = <?= json_encode($instructors) ?>;
        window.classList = <?= json_encode($classes) ?>;
    </script>
</div>