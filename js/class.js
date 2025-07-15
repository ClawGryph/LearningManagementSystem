function initClass(){
    // Handle Edit and Delete actions in the Class Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var classsID = row.getAttribute('data-class-id');
            var sectionCell = row.children[2];
            var maxStudentCell = row.children[3];
            var actionsCell = row.children[4];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldSection = sectionCell.textContent.trim();
                var oldMaxNumber = maxStudentCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldSection = oldSection;
                btn.dataset.oldMaxNumber = oldMaxNumber;

                // Replace with input fields
                sectionCell.innerHTML = "<input type='text' value='" + oldSection + "'>";
                maxStudentCell.innerHTML = "<input type='text' value='" + oldMaxNumber + "'>";

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newSection = sectionCell.querySelector("input").value.trim();
                var newMaxStudent = maxStudentCell.querySelector("input").value.trim();
                var oldSection = btn.dataset.oldSection;

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editClass.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        sectionCell.textContent = newSection;
                        maxStudentCell.textContent = newMaxStudent;
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
                xhr.send("class_Id=" + encodeURI(classsID) +
                         "&oldSection=" + encodeURIComponent(oldSection) +
                         "&section=" + encodeURIComponent(newSection) +
                         "&maxStudent=" + encodeURIComponent(newMaxStudent));
            }
        });
    });


    document.getElementById("classTable").addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".deleteBtn");
        if (deleteBtn) {
            if (!confirm("Are you sure you want to delete this Class?")) return;

            const row = deleteBtn.closest("tr");
            const classID = row.getAttribute('data-class-id');
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/deleteClass.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === "success") {
                    alert("Delete successful!");
                    row.remove();
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("classId=" + encodeURIComponent(classID));
        }
    });
}