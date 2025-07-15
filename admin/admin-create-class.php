<div class="home-content">
    <div class="sidebar-toggle">
        <i class="fa-solid fa-bars"></i>
        <span class="menu-text">Drop Down Sidebar</span>
    </div>
    <div class="content-container">
        <div class="first-page">
            <h2>Class</h2>
            <div class="class-management-container">
                <!-- CLASS LIST -->
                <div class="class-list-container">
                    <div class="table-container">
                        <table class="table-content">
                            <thead>
                                <th>No.</th>
                                <th>Year</th>
                                <th>Section</th>
                                <th>Maximum Students</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="table-body">
                                <!-- Section and maximum students -->
                                <?php
                                    include '../db.php';
    
                                    $stmt = $conn->prepare("SELECT * from class;");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
    
                                    if ($result->num_rows > 0) {
                                        $count = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-class-id='{$row['classID']}'>
                                                    <td>{$count}</td>
                                                    <td>{$row['year']}</td>
                                                    <td>{$row['section']}</td>
                                                    <td>{$row['maxStudent']}</td>
                                                    <td>
                                                        <button type='button' class='home-contentBtn editBtn btn-accent-bg'><i class='fa-solid fa-pen-to-square'></i></button>
                                                        <button type='button' class='home-contentBtn deleteBtn btn-drk-bg'><i class='fa-solid fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                            $count++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No class found.</td></tr>";
                                    }
    
                                    $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CREATE CLASS -->
                <div class="create-class-container">
                    <form action="../action/addNewClass.php" class="form-content" method="POST">
                        <h2>Create Class</h2>
                        <select name="classYear" id="classYear">
                            <option value="">Select Year</option>
                            <option value="1">1</option>
                        </select>
                        <div>
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <input type="text" name="classSection" maxlength="10" placeholder="Section name">
                        </div>
                        <div>
                            <i class="fa-solid fa-users"></i>
                            <input type="number" name="classMaxStudent" min="1" max="100" placeholder="maximum number of students">
                        </div>
                        <button type="submit" class="home-contentBtn btn-accent-bg">Create Class</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>