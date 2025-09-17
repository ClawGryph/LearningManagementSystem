function initInstructorLoad(){
    // Handle Edit and Delete actions in the Purchase Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var instructorCourseID = row.getAttribute('data-instructor_course-id');
            var courseCodeCell = row.children[2];
            var instructorNameCell = row.children[3];
            var classCell = row.children[4];
            var actionsCell = row.children[6];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldCourseCode = courseCodeCell.textContent.trim();
                var oldInstructorName = instructorNameCell.textContent.trim();
                var oldClass = classCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldCourseCode = oldCourseCode;
                btn.dataset.oldInstructorName = oldInstructorName;
                btn.dataset.oldClass = oldClass;

                // Replace with input fields
                // DROPDOWN FOR Courses
                let selectCourseHTML = "<select>";
                courseList.forEach(course => {
                    const courseName = course.courseCode + "-" + course.courseName;
                    const selectedCourse = courseName === oldCourseCode ? "selected" : "";
                    selectCourseHTML += `<option value="${course.courseID}" ${selectedCourse}>${courseName}</option>`;
                });
                selectCourseHTML += "</select>";

                courseCodeCell.innerHTML = selectCourseHTML;

                // DROPDOWN FOR INSTRUCTOR NAME
                let selectHTML = "<select>";
                instructorList.forEach(instructor => {
                    const fullName = instructor.fullName;
                    const selected = fullName === oldInstructorName ? "selected" : "";
                    selectHTML += `<option value="${instructor.userID}" ${selected}>${fullName}</option>`;
                });
                selectHTML += "</select>";

                instructorNameCell.innerHTML = selectHTML;

                // DROPDOWN FOR CLASS NAME
                let selectClassHTML = "<select>";
                classList.forEach(classes => {
                    const className = classes.year + "-" + classes.section;
                    const selectedClass = className === oldClass ? "selected" : "";
                    selectClassHTML += `<option value="${classes.classID}" ${selectedClass}>${className}</option>`;
                });
                selectClassHTML += "</select>";

                classCell.innerHTML = selectClassHTML;

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newCourseID = courseCodeCell.querySelector("select").value;
                var newIntructorID = instructorNameCell.querySelector("select").value;
                var newClassID = classCell.querySelector("select").value;
                var oldCourseCode = btn.dataset.oldCourseCode;

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editInstructorCourse.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        if (xhr.responseText === "duplicate") {
                            alert("This instructor is already assigned to that course and class.");
                            return;
                        }

                        //Store new values
                        const selectedCourse = courseList.find(c => c.courseID == newCourseID);
                        courseCodeCell.textContent = selectedCourse
                            ? `${selectedCourse.courseCode}-${selectedCourse.courseName}`
                            : "Unknown";
                            
                        instructorNameCell.textContent = instructorList.find(i => i.userID == newIntructorID)?.fullName || "Unknown";
                        const selectedClass = classList.find(cl => cl.classID == newClassID);
                        classCell.textContent = selectedClass
                            ? `${selectedClass.year}-${selectedClass.section}`
                            : "Unknown";

                        btn.innerHTML = "<i class='fa-solid fa-pen-to-square'></i>";
                        btn.classList.add("editBtn");
                        btn.classList.remove("saveBtn");

                        // Restore the Delete button
                        if (!actionsCell.querySelector('.deleteBtn')) {
                            var deleteBtn = document.createElement('button');
                            deleteBtn.type = "button";
                            deleteBtn.classList.add("home-contentBtn", "deleteBtn", "btn-drk-bg");
                            deleteBtn.innerHTML = "<i class='fa-solid fa-trash'></i>";
                            actionsCell.appendChild(deleteBtn);
                        }

                        alert("Update successful!");
                    } else {
                        alert("Update failed!");
                    }
                };
                xhr.send("instructor_courseID=" + encodeURI(instructorCourseID) +
                         "&courseID=" + encodeURIComponent(newCourseID) +
                         "&instructorID=" + encodeURIComponent(newIntructorID) +
                         "&classID=" + encodeURIComponent(newClassID));
            }
        });
    });


    document.getElementById("instructor-load-table").addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".deleteBtn");
        if (deleteBtn) {
            if (!confirm("Are you sure you want to delete this Teaching Load?")) return;

            const row = deleteBtn.closest("tr");
            const instructorCourseID = row.getAttribute('data-instructor_course-id');
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/deleteInstructorCourse.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === "success") {
                    row.remove();
                    alert("Delete successful!");
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("instructor_courseID=" + encodeURIComponent(instructorCourseID));
        }
    });   
}