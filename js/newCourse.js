function initNewCourse(){

    toggleUserManagementView();

    function toggleUserManagementView() {
        const firstPage = document.querySelector('.first-page');
        const courseModal = document.getElementById('courseModal');

        //Show modal, hide first page
        document.getElementById('coursePage').addEventListener('click', function() {
            firstPage.classList.add('hidden');
            courseModal.classList.add('active');
        });

        //Hide modal, show first page
        document.getElementById('addNewCourse').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission
            courseModal.classList.remove('active');
            firstPage.classList.remove('hidden');
        });
    }

    // Handle Edit and Delete actions in the Purchase Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var instructorCourseID = row.getAttribute('data-instructor_course-id');
            var courseCodeCell = row.children[1];
            var courseNameCell = row.children[2];
            var instructorNameCell = row.children[3];
            var actionsCell = row.children[5];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldCourseCode = courseCodeCell.textContent.trim();
                var oldCourseName = courseNameCell.textContent.trim();
                var oldInstructorName = instructorNameCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldCourseCode = oldCourseCode;
                btn.dataset.oldCourseName = oldCourseName;
                btn.dataset.oldInstructorName = oldInstructorName;

                // Replace with input fields
                courseCodeCell.innerHTML = "<input type='text' value='" + oldCourseCode + "'>";
                courseNameCell.innerHTML = "<input type='text' value='" + oldCourseName + "'>";
                // DROPDOWN FOR INSTRUCTOR NAME
                let selectHTML = "<select>";
                instructorList.forEach(instructor => {
                    const fullName = instructor.fullName;
                    const selected = fullName === oldInstructorName ? "selected" : "";
                    selectHTML += `<option value="${instructor.userID}" ${selected}>${fullName}</option>`;
                });
                selectHTML += "</select>";

                instructorNameCell.innerHTML = selectHTML;

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newCourseCode = courseCodeCell.querySelector("input").value.trim();
                var newCourseName = courseNameCell.querySelector("input").value.trim();
                var newIntructorID = instructorNameCell.querySelector("select").value;
                var oldCourseCode = btn.dataset.oldCourseCode;

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editInstructorCourse.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        courseCodeCell.textContent = newCourseCode;
                        courseNameCell.textContent = newCourseName;
                        instructorNameCell.textContent = instructorList.find(i => i.userID == newIntructorID)?.fullName || "Unknown";
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
                xhr.send("instructor_courseID=" + encodeURI(instructorCourseID) +
                         "&oldCourseCode=" + encodeURIComponent(oldCourseCode) +
                         "&courseCode=" + encodeURIComponent(newCourseCode) +
                         "&courseName=" + encodeURIComponent(newCourseName) +
                         "&instructorID=" + encodeURIComponent(newIntructorID));
            }
        });
    });


    document.getElementById("courseTable").addEventListener("click", function (e) {
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
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("instructor_courseID=" + encodeURIComponent(instructorCourseID));
        }
    });   
}