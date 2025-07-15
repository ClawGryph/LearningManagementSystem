function initCourse(){
    // Handle Edit and Delete actions in the Class Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var courseID = row.getAttribute('data-course-id');
            var courseCodeCell = row.children[1];
            var courseNameCell = row.children[2];
            var actionsCell = row.children[3];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldCourseCode = courseCodeCell.textContent.trim();
                var oldCourseName = courseNameCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldCourseCode = oldCourseCode;
                btn.dataset.oldCourseName = oldCourseName;

                // Replace with input fields
                courseCodeCell.innerHTML = "<input type='text' value='" + oldCourseCode + "'>";
                courseNameCell.innerHTML = "<input type='text' value='" + oldCourseName + "'>";

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newCourseCode = courseCodeCell.querySelector("input").value.trim();
                var newCourseName = courseNameCell.querySelector("input").value.trim();

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editCourse.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        courseCodeCell.textContent = newCourseCode;
                        courseNameCell.textContent = newCourseName;
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
                    } else {
                        alert("Update failed!");
                    }
                };
                xhr.send("course_Id=" + encodeURI(courseID) +
                         "&courseCode=" + encodeURIComponent(newCourseCode) +
                         "&courseName=" + encodeURIComponent(newCourseName));
            }
        });
    });


    document.getElementById("courseTable").addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".deleteBtn");
        if (deleteBtn) {
            if (!confirm("Are you sure you want to delete this Course?")) return;

            const row = deleteBtn.closest("tr");
            const courseID = row.getAttribute('data-course-id');
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/deleteCourse.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === "success") {
                    alert("Delete successfully!");
                    row.remove();
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("course_Id=" + encodeURIComponent(courseID));
        }
    });
}