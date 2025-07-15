<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page" id="courseModal">
            <h2>Course</h2>
            <div class="class-management-container">
                <!-- COURSE LIST -->
                <div class="class-list-container">
                    <div class="table-container">
                        <table class="table-content">
                            <thead>
                                <th>No.</th>
                                <th>Course Code</th>
                                <th>Name</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="table-body">
                                <!-- Section and maximum students -->
                                <?php
                                    include '../db.php';
    
                                    $stmt = $conn->prepare("SELECT * from courses;");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
    
                                    if ($result->num_rows > 0) {
                                        $count = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-class-id='{$row['courseID']}'>
                                                    <td>{$count}</td>
                                                    <td>{$row['courseCode']}</td>
                                                    <td>{$row['courseName']}</td>
                                                    <td>
                                                        <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                        <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                            $count++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No course found.</td></tr>";
                                    }
    
                                    $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- COURSE FORM -->
                <div class="create-class-container">
                    <form action="../action/addNewCourse.php" class="form-content" method="POST">
                        <h2>Create Course</h2>
                        <div>
                            <i class="fa-solid fa-book"></i>
                            <input type="text" id="courseCode" name="courseCode" placeholder="Enter course code" required>
                        </div>
                        <div>
                            <i class="fa-solid fa-keyboard"></i>
                            <input type="text" id="courseName" name="courseName" placeholder="Enter course name" required>
                        </div>
                        <button type="submit" id="submitCourse" class="home-contentBtn btn-accent-bg">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>