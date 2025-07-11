<div>
    <div>
        <!-- FIRST PAGE !-->
        <div>
            <h2>Create Courses</h2>
            <div>
                <button type="submit">Create new course</button>
            </div>
            <table>
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
                <tbody>
                    <?php
                    include '../db.php';

                    $stmt = $conn->prepare("SELECT c.courseCode, c.courseName, CONCAT(i.firstName, ' ', i.lastName) AS 'Instructors_Name', ic.code from instructor_courses ic JOIN courses c ON ic.courseID = c.courseID JOIN users i ON ic.instructorID = i.userID;");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$count}</td>
                                    <td>{$row['courseCode']}</td>
                                    <td>{$row['courseName']}</td>
                                    <td>{$row['Instructors_Name']}</td>
                                    <td>{$row['code']}</td>
                                    <td>
                                        <button type='button' class='editBtn'><i class='fa-solid fa-pen-to-square'></i></button>
                                        <button type='button' class='deleteBtn'><i class='fa-solid fa-trash'></i></button>
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

        <!-- SECOND PAGE !-->
        <div>
            <div>
                <a href="#" data-content="admin-create-courses.php"><i class="fa-solid fa-circle-arrow-left"></i></a>
                <h2>Create New Course</h2>
            </div>
            <form action="../action/addNewCourse.php" method="POST">
                <div>
                    <i class="fa-solid fa-book"></i>
                    <input type="text" id="courseCode" name="courseCode" placeholder="Enter course code" required>
                </div>
                <div>
                    <i class="fa-solid fa-keyboard"></i>
                    <input type="text" id="courseName" name="courseName" placeholder="Enter course name" required>
                </div>
                <button type="submit">Create Course</button>
            </form>
        </div>
    </div>
</div>