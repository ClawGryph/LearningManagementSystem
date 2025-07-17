function initQuiz(){

    toggleUserManagementView();

    function toggleUserManagementView() {
        const firstPage = document.querySelector(".first-page");
        const addQuiz = document.getElementById("addQuizModal");
        const addQuizToClass = document.getElementById("addQuizToClassModal");
        const questions = document.getElementById("questions");

        // Show add quiz modal, hide main table
        document.getElementById("addQuizPage").addEventListener("click", function () {
            firstPage.classList.add("hidden");
            addQuiz.classList.add("active");
        });

        // Show add quiz to class modal, hide main table
        document.getElementById("addQuizToClassPage").addEventListener("click", function () {
            firstPage.classList.add("hidden");
            addQuizToClass.classList.add("active");
        });

        // Show questions modal, hide main table
        document.getElementById("questionPage").addEventListener("click", function () {
            firstPage.classList.add("hidden");
            questions.classList.add("active");
        });


        // Back button
        // Add quiz
        document.querySelector("#addQuizModal a[data-content='instructor-create-quiz.php']").addEventListener("click", function (e) {
            e.preventDefault();
            addQuiz.classList.remove("active");
            firstPage.classList.remove("hidden");
        });

        // Add quiz to Class
        document.querySelector("#addQuizToClassModal a[data-content='instructor-create-quiz.php']").addEventListener("click", function (e) {
            e.preventDefault();
            addQuizToClass.classList.remove("active");
            firstPage.classList.remove("hidden");
        });

        // Questions
        document.querySelector("#questions a[data-content='instructor-create-quiz.php']").addEventListener("click", function (e) {
            e.preventDefault();
            questions.classList.remove("active");
            firstPage.classList.remove("hidden");
        });
    }

    // Handle Edit and Delete actions in the Class Table
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function handler() {
            var row = btn.closest('tr');
            var quizID = row.getAttribute('data-quiz-id');
            var titleCell = row.children[0];
            var descriptionCell = row.children[1];
            var deadlineCell = row.children[2];
            var actionsCell = row.children[4];

            if (btn.classList.contains("editBtn")) {
                // Remove the Delete button
                var deleteBtn = actionsCell.querySelector('button:not(.editBtn):not(.saveBtn)');
                if (deleteBtn) {
                    deleteBtn.remove();
                }

                // Save current values BEFORE replacing with inputs
                var oldTitle = titleCell.textContent.trim();
                var oldDescription = descriptionCell.textContent.trim();
                var oldDeadline = deadlineCell.textContent.trim();

                // Store oldUsername as a data attribute on the button
                btn.dataset.oldTitle = oldTitle;
                btn.dataset.oldDescription = oldDescription;
                btn.dataset.oldDeadline = oldDeadline;

                // Replace with input fields
                titleCell.innerHTML = "<input type='text' value='" + oldTitle + "' required>";
                descriptionCell.innerHTML = "<input type='text' value='" + oldDescription + "' required>";
                deadlineCell.innerHTML = "<input type='datetime-local' value='" + oldDeadline + "' required>";

                // Change Edit to Save
                btn.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
                btn.classList.add("icon", "saveBtn");
                btn.classList.remove("editBtn");
            } else {
                // On Save
                var newTitle = titleCell.querySelector("input").value.trim();
                var newDescription = descriptionCell.querySelector("input").value.trim();
                var newDeadline = deadlineCell.querySelector("input").value.trim();

                // AJAX to update
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/editQuiz.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        titleCell.textContent = newTitle;
                        descriptionCell.textContent = newDescription;
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
                xhr.send("quiz_Id=" + encodeURI(quizID) +
                         "&title=" + encodeURIComponent(newTitle) +
                         "&description=" + encodeURIComponent(newDescription) +
                         "&deadline=" + encodeURIComponent(newDeadline));
            }
        });
    });


    document.getElementById("quizTable").addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".deleteBtn");
        if (deleteBtn) {
            if (!confirm("Are you sure you want to delete this quiz?")) return;

            const row = deleteBtn.closest("tr");
            const quizID = row.getAttribute('data-quiz-id');
            const xhr = new XMLHttpRequest();
        xhr.open("POST", "../action/deleteQuiz.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === "success") {
                    alert("Delete successful!");
                    row.remove();
                } else {
                    alert("Delete failed!");
                }
            };
            xhr.send("quizId=" + encodeURIComponent(quizID));
        }
    });
}