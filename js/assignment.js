function initAssignment(){
    toggleUserManagementView();

    function toggleUserManagementView() {
        const firstPage = document.querySelector(".first-page");
        const secondPage = document.querySelector(".second-page");

        // Show add quiz to class modal, hide main table
        document.getElementById("addAssignment").addEventListener("click", function () {
            firstPage.classList.add("hidden");
            secondPage.classList.add("active");
        });

        // Back button
        // Add assignment to Class
        document.querySelector("#assignmentModal a[data-content='instructor-create-assignment.php']").addEventListener("click", function (e) {
            e.preventDefault();
            secondPage.classList.remove("active");
            firstPage.classList.remove("hidden");
        });
    }

    // Handle Edit and Delete actions in the Class Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var assignmentID = row.getAttribute('data-assignment-id');
            var titleCell = row.children[0];
            var deadlineCell = row.children[3];
            var actionsCell = row.children[4];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldTitle = titleCell.textContent.trim();
                var oldDeadline = deadlineCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldTitle = oldTitle;
                btn.dataset.oldDeadline = oldDeadline;

                // Replace with input fields
                titleCell.innerHTML = "<input type='text' value='" + oldTitle + "' required>";
                deadlineCell.innerHTML = "<input type='datetime-local' value='" + oldDeadline + "' required>";

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newTitle = titleCell.querySelector("input").value.trim();
                var newDeadline = deadlineCell.querySelector("input").value.trim();

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editAssignment.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        titleCell.textContent = newTitle;
                        deadlineCell.textContent = newDeadline;
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
                        alert("Update sucessful!");
                    } else {
                        alert("Update failed!");
                    }
                };
                xhr.send("assignment_Id=" + encodeURI(assignmentID) +
                         "&title=" + encodeURIComponent(newTitle) +
                         "&deadline=" + encodeURIComponent(newDeadline));
            }
        });
    });


    document.getElementById("assignmentTable").addEventListener("click", function (e) {

        const deleteBtn = e.target.closest(".deleteBtn");
        if (deleteBtn) {
            if (!confirm("Are you sure you want to delete this assignment?")) return;

            const row = deleteBtn.closest("tr");
            const assignmentID = row.getAttribute('data-assignment-id');
            const xhr = new XMLHttpRequest();
        xhr.open("POST", "../action/deleteAssignment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                console.log("Response:", xhr.responseText);
                
                if (xhr.status === 200 && xhr.responseText === "success") {
                    row.remove();
                    alert("Delete successful!");
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("assignment_Id=" + encodeURIComponent(assignmentID));
        }
    });
}